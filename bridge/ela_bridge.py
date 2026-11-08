#!/usr/bin/env python3
"""
ELA-Bridge: verbindet den MQTT-Broker (Mosquitto) mit der Datenbank.

Stufe 1: Status-Meldungen der Controller empfangen, last_seen setzen.
Stufe 2: Befehle aus der Tabelle device_commands senden (cmd / stop),
         Quittungen (ack) zurückschreiben.

Das Protokoll steht in docs/mqtt.md.
Konfiguration über Umgebungsvariablen, siehe bridge.env.example.
"""

from __future__ import annotations

import json
import logging
import os
import signal
import sys
import threading
from typing import Optional

import paho.mqtt.client as mqtt
import pymysql

log = logging.getLogger("ela-bridge")

TOPIC_STATUS = "ela/+/status"
TOPIC_ACK = "ela/+/ack"

DISPATCH_INTERVAL_S = 2
DISPATCH_BATCH = 20
MAX_COMMAND_AGE_S = 120
ACK_STATES = ("started", "done", "error")


def env(name: str, default: Optional[str] = None, required: bool = False) -> str:
    value = os.environ.get(name, default)
    if required and (value is None or value == ""):
        log.error("Pflichtvariable fehlt: %s", name)
        sys.exit(2)
    return value or ""


class Database:
    """Eine Verbindung pro Instanz. Jede Instanz nur aus einem Thread benutzen."""

    def __init__(self) -> None:
        self._conn: Optional[pymysql.connections.Connection] = None
        self._settings = dict(
            host=env("ELA_DB_HOST", "127.0.0.1"),
            port=int(env("ELA_DB_PORT", "3306")),
            user=env("ELA_BRIDGE_DB_USER", required=True),
            password=env("ELA_BRIDGE_DB_PASSWORD", required=True),
            database=env("ELA_DB_NAME", "ela"),
            charset="utf8mb4",
            autocommit=True,
        )

    def _connect(self) -> pymysql.connections.Connection:
        conn = pymysql.connect(**self._settings)
        # Zeitstempel in UTC, wie in der Anwendung
        with conn.cursor() as cur:
            cur.execute("SET time_zone = '+00:00'")
        return conn

    def connection(self) -> pymysql.connections.Connection:
        if self._conn is None:
            self._conn = self._connect()
        else:
            self._conn.ping(reconnect=True)
        return self._conn


# Getrennte Verbindungen: eine für den Netzwerk-Thread (Status/Acks),
# eine für den Versand-Thread (Befehle).
db_status = Database()
db_dispatch = Database()


def touch_last_seen(device_id: str) -> bool:
    """Setzt last_seen auf jetzt. False, wenn das Gerät unbekannt ist."""
    with db_status.connection().cursor() as cur:
        cur.execute(
            "UPDATE controllers SET last_seen = UTC_TIMESTAMP() WHERE device_id = %s",
            (device_id,),
        )
        return cur.rowcount > 0


def store_ack(device_id: str, cmd_id: int, state: str, detail: str) -> bool:
    """Trägt die Quittung eines Controllers ein. False, wenn der Befehl nicht passt."""
    with db_status.connection().cursor() as cur:
        cur.execute(
            """
            UPDATE device_commands dc
            JOIN controllers c ON c.id = dc.controller_id
            SET dc.status = %s,
                dc.detail = %s,
                dc.acked_at = UTC_TIMESTAMP()
            WHERE dc.id = %s AND c.device_id = %s
            """,
            (state, detail or None, cmd_id, device_id),
        )
        return cur.rowcount > 0


def on_connect(client, userdata, flags, reason_code, properties) -> None:
    if reason_code.is_failure:
        log.error("Verbindung zum Broker fehlgeschlagen: %s", reason_code)
        return
    log.info("Mit Broker verbunden, abonniere %s und %s", TOPIC_STATUS, TOPIC_ACK)
    client.subscribe([(TOPIC_STATUS, 1), (TOPIC_ACK, 1)])


def on_disconnect(client, userdata, flags, reason_code, properties) -> None:
    log.warning("Verbindung zum Broker getrennt: %s", reason_code)


def on_message(client, userdata, msg: mqtt.MQTTMessage) -> None:
    parts = msg.topic.split("/")
    if len(parts) != 3 or parts[0] != "ela":
        return

    device_id, kind = parts[1], parts[2]

    try:
        payload = json.loads(msg.payload.decode("utf-8"))
    except (UnicodeDecodeError, ValueError):
        log.warning("Ungültige Nachricht von %s (%s): %r", device_id, kind, msg.payload[:120])
        return

    if not isinstance(payload, dict):
        log.warning("Nachricht von %s (%s) ist kein Objekt", device_id, kind)
        return

    if kind == "status":
        handle_status(device_id, payload)
    elif kind == "ack":
        handle_ack(device_id, payload)


def handle_status(device_id: str, payload: dict) -> None:
    state = payload.get("state")

    if state == "online":
        try:
            known = touch_last_seen(device_id)
        except pymysql.MySQLError:
            log.exception("Datenbankfehler bei last_seen von %s", device_id)
            return

        if not known:
            log.warning("Unbekanntes Gerät: %s (kein Controller mit dieser Geräte-ID)", device_id)

    elif state == "offline":
        log.info("Gerät offline: %s", device_id)

    else:
        log.warning("Unbekannter Status von %s: %r", device_id, state)


def handle_ack(device_id: str, payload: dict) -> None:
    cmd_id = payload.get("id")
    state = payload.get("state")
    detail = str(payload.get("detail") or "")[:500]

    if not isinstance(cmd_id, int) or state not in ACK_STATES:
        log.warning("Ungültige Quittung von %s: %r", device_id, payload)
        return

    try:
        known = store_ack(device_id, cmd_id, state, detail)
    except pymysql.MySQLError:
        log.exception("Datenbankfehler bei Quittung %s von %s", cmd_id, device_id)
        return

    if known:
        log.info("Quittung %s von %s: %s", cmd_id, device_id, state)
    else:
        log.warning("Quittung zu unbekanntem Befehl %s von %s", cmd_id, device_id)


def dispatch_pending(client: mqtt.Client) -> None:
    """Sendet wartende Befehle. Läuft im eigenen Thread."""
    conn = db_dispatch.connection()

    with conn.cursor() as cur:
        cur.execute(
            """
            SELECT dc.id, dc.command, dc.payload, c.device_id, c.enabled,
                   TIMESTAMPDIFF(SECOND, dc.created_at, UTC_TIMESTAMP()) AS age_s
            FROM device_commands dc
            JOIN controllers c ON c.id = dc.controller_id
            WHERE dc.status = 'pending'
            ORDER BY dc.id
            LIMIT %s
            """,
            (DISPATCH_BATCH,),
        )
        rows = cur.fetchall()

        for cmd_id, command, payload_json, device_id, enabled, age_s in rows:
            if int(enabled) != 1:
                _mark(cur, cmd_id, "error", "Controller ist deaktiviert")
                continue

            if int(age_s) > MAX_COMMAND_AGE_S:
                _mark(cur, cmd_id, "error", f"Befehl zu alt ({int(age_s)} s), nicht gesendet")
                continue

            payload = json.loads(payload_json or "{}")
            payload["id"] = int(cmd_id)

            topic = f"ela/{device_id}/" + ("stop" if command == "stop" else "cmd")
            result = client.publish(topic, json.dumps(payload), qos=1)

            if result.rc == mqtt.MQTT_ERR_SUCCESS:
                cur.execute(
                    "UPDATE device_commands SET status = 'sent', sent_at = UTC_TIMESTAMP() "
                    "WHERE id = %s AND status = 'pending'",
                    (cmd_id,),
                )
                log.info("Befehl %s an %s gesendet (%s)", cmd_id, device_id, topic)
            else:
                log.warning("Befehl %s konnte nicht gesendet werden: rc=%s", cmd_id, result.rc)


def _mark(cur, cmd_id: int, status: str, detail: str) -> None:
    cur.execute(
        "UPDATE device_commands SET status = %s, detail = %s WHERE id = %s AND status = 'pending'",
        (status, detail, cmd_id),
    )
    log.warning("Befehl %s: %s", cmd_id, detail)


def dispatch_loop(client: mqtt.Client, stop: threading.Event) -> None:
    while not stop.is_set():
        try:
            dispatch_pending(client)
        except pymysql.MySQLError:
            log.exception("Datenbankfehler beim Versand, neuer Versuch in %s s", DISPATCH_INTERVAL_S)
        except Exception:
            log.exception("Unerwarteter Fehler beim Versand")
        stop.wait(DISPATCH_INTERVAL_S)


def main() -> None:
    logging.basicConfig(
        level=os.environ.get("ELA_BRIDGE_LOG_LEVEL", "INFO").upper(),
        format="%(asctime)s %(levelname)s %(name)s: %(message)s",
    )

    client = mqtt.Client(
        mqtt.CallbackAPIVersion.VERSION2,
        client_id=env("ELA_MQTT_CLIENT_ID", "ela-bridge"),
    )
    client.username_pw_set(
        env("ELA_MQTT_USER", required=True),
        env("ELA_MQTT_PASSWORD", required=True),
    )
    client.on_connect = on_connect
    client.on_disconnect = on_disconnect
    client.on_message = on_message

    stop = threading.Event()

    def shutdown(signum, frame) -> None:
        log.info("Beende Bridge (Signal %s)", signum)
        stop.set()
        client.disconnect()

    signal.signal(signal.SIGTERM, shutdown)
    signal.signal(signal.SIGINT, shutdown)

    client.connect(
        env("ELA_MQTT_HOST", "127.0.0.1"),
        int(env("ELA_MQTT_PORT", "1883")),
        keepalive=60,
    )

    threading.Thread(
        target=dispatch_loop,
        args=(client, stop),
        name="dispatch",
        daemon=True,
    ).start()

    client.loop_forever(retry_first_connection=True)


if __name__ == "__main__":
    main()
