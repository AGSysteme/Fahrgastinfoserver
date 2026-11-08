#!/usr/bin/env python3
"""
ELA-Bridge: verbindet den MQTT-Broker (Mosquitto) mit der Datenbank.

Stufe 1 (aktuell):
  - Status-Meldungen der Controller empfangen (ela/<device_id>/status)
  - bei "online" die Spalte controllers.last_seen aktualisieren (UTC)

Spätere Stufen (noch nicht enthalten):
  - Befehle für Maßnahmen senden (auf Basis von Ela\\Dispatch::planFor)
  - Quittungen (ela/<device_id>/ack) verarbeiten

Konfiguration über Umgebungsvariablen, siehe bridge.env.example.
"""

from __future__ import annotations

import json
import logging
import os
import signal
import sys
from typing import Optional

import paho.mqtt.client as mqtt
import pymysql

log = logging.getLogger("ela-bridge")

TOPIC_STATUS = "ela/+/status"


def env(name: str, default: Optional[str] = None, required: bool = False) -> str:
    value = os.environ.get(name, default)
    if required and (value is None or value == ""):
        log.error("Pflichtvariable fehlt: %s", name)
        sys.exit(2)
    return value or ""


class Database:
    """Eine Verbindung, die bei Bedarf neu aufgebaut wird."""

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

    def touch_last_seen(self, device_id: str) -> bool:
        """Setzt last_seen auf jetzt. Gibt False zurück, wenn das Gerät unbekannt ist."""
        conn = self.connection()
        with conn.cursor() as cur:
            cur.execute(
                "UPDATE controllers SET last_seen = UTC_TIMESTAMP() WHERE device_id = %s",
                (device_id,),
            )
            return cur.rowcount > 0


db = Database()


def on_connect(client, userdata, flags, reason_code, properties) -> None:
    if reason_code.is_failure:
        log.error("Verbindung zum Broker fehlgeschlagen: %s", reason_code)
        return
    log.info("Mit Broker verbunden, abonniere %s", TOPIC_STATUS)
    client.subscribe(TOPIC_STATUS, qos=1)


def on_disconnect(client, userdata, flags, reason_code, properties) -> None:
    log.warning("Verbindung zum Broker getrennt: %s", reason_code)


def on_message(client, userdata, msg: mqtt.MQTTMessage) -> None:
    parts = msg.topic.split("/")
    if len(parts) != 3 or parts[0] != "ela" or parts[2] != "status":
        return

    device_id = parts[1]

    try:
        payload = json.loads(msg.payload.decode("utf-8"))
    except (UnicodeDecodeError, ValueError):
        log.warning("Ungültige Status-Meldung von %s: %r", device_id, msg.payload[:120])
        return

    state = payload.get("state")

    if state == "online":
        try:
            known = db.touch_last_seen(device_id)
        except pymysql.MySQLError:
            log.exception("Datenbankfehler bei last_seen von %s", device_id)
            return

        if not known:
            log.warning("Unbekanntes Gerät: %s (kein Controller mit dieser Geräte-ID)", device_id)
        else:
            log.debug("last_seen aktualisiert: %s", device_id)

    elif state == "offline":
        # Last-Will des Brokers oder Abmeldung des Geräts
        log.info("Gerät offline: %s", device_id)

    else:
        log.warning("Unbekannter Status von %s: %r", device_id, state)


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

    def stop(signum, frame) -> None:
        log.info("Beende Bridge (Signal %s)", signum)
        client.disconnect()

    signal.signal(signal.SIGTERM, stop)
    signal.signal(signal.SIGINT, stop)

    client.connect(
        env("ELA_MQTT_HOST", "127.0.0.1"),
        int(env("ELA_MQTT_PORT", "1883")),
        keepalive=60,
    )

    client.loop_forever(retry_first_connection=True)


if __name__ == "__main__":
    main()
