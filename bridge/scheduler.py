"""
Maßnahmen-Scheduler der Bridge.

Prüft alle SCHED_INTERVAL_S Sekunden, welche Maßnahmen fällig sind, und legt
für jeden Controller der Zielhaltestellen einen Befehl "play" in
device_commands an. Die Bridge sendet diese Befehle (ela_bridge.py).

Regeln:
  - Nur aktive Maßnahmen (measures.active = 1).
  - "einmalig": fällig zum Startzeitpunkt, innerhalb ONCE_GRACE_S.
  - "wiederholt": Termine alle repeat_interval Minuten ab start_at bis end_at,
    jeder Termin innerhalb SLOT_GRACE_S nach seinem Zeitpunkt.
  - "Nachts deaktivieren": keine Auslösung zwischen NIGHT_START und NIGHT_END (Berlin).
  - Jeder Termin wird genau einmal ausgelöst (measure_runs, eindeutiger Schlüssel).
  - Nur LAN-Controller mit Ausgang und aktiviert. RS485 folgt später.
  - Ansage wird als 16 kHz / 16 Bit / mono nach AUDIO_DEVICE_DIR exportiert
    und per URL geliefert.
"""

from __future__ import annotations

import json
import logging
import os
import wave
import subprocess
from datetime import datetime, timedelta
from zoneinfo import ZoneInfo

import pymysql

log = logging.getLogger("ela-bridge.sched")

TZ = ZoneInfo("Europe/Berlin")

SCHED_INTERVAL_S = 15
ONCE_GRACE_S = 120
SLOT_GRACE_S = 90
NIGHT_START = 22  # Uhr, Berlin
NIGHT_END = 6
MAX_MS_MARGIN = 5000

AUDIO_RATE = 16000

AUDIO_BASE_URL = os.environ.get("ELA_AUDIO_BASE_URL", "http://192.168.3.104:8080/audio")
AUDIO_SRC_DIR = os.environ.get("ELA_AUDIO_SRC_DIR", "/opt/ela/audio/announcements")
AUDIO_DEVICE_DIR = os.environ.get("ELA_AUDIO_DEVICE_DIR", "/opt/ela/audio/device")


def now_local() -> datetime:
    """Aktuelle Berlin-Wandzeit, ohne Zeitzonenangabe (wie in der Datenbank)."""
    return datetime.now(TZ).replace(tzinfo=None)


def parse_local(value: str) -> datetime:
    """Akzeptiert 'YYYY-MM-DDTHH:MM' und 'YYYY-MM-DD HH:MM:SS'."""
    value = value.strip()
    for fmt in ("%Y-%m-%dT%H:%M", "%Y-%m-%d %H:%M:%S", "%Y-%m-%d %H:%M"):
        try:
            return datetime.strptime(value, fmt)
        except ValueError:
            continue
    raise ValueError(f"Ungültige Zeitangabe: {value!r}")


def in_night(moment: datetime) -> bool:
    h = moment.hour
    if NIGHT_START > NIGHT_END:
        return h >= NIGHT_START or h < NIGHT_END
    return NIGHT_START <= h < NIGHT_END


def due_slot(measure: dict, now: datetime) -> datetime | None:
    """Gibt den fälligen Termin zurück oder None."""
    start = parse_local(measure["start_at"])
    repeat = measure["repeat_type"]

    if repeat == "once":
        since = (now - start).total_seconds()
        return start if 0 <= since <= ONCE_GRACE_S else None

    if measure["end_at"]:
        end = parse_local(measure["end_at"])
    else:
        end = None

    interval_min = max(1, int(measure["repeat_interval"] or 1))
    delta = (now - start).total_seconds()
    if delta < 0:
        return None

    k = int(delta // (interval_min * 60))
    slot = start + timedelta(minutes=interval_min * k)
    if end is not None and slot > end:
        return None

    since = (now - slot).total_seconds()
    return slot if 0 <= since <= SLOT_GRACE_S else None


def ensure_device_audio(conn, audio_id: int) -> tuple[str, int]:
    """
    Sorgt dafür, dass AUDIO_DEVICE_DIR/NNNN.wav im Controller-Format existiert.
    Gibt (Dateiname, Dauer in ms) zurück. Wirft Exception bei Fehler.
    """
    if not 0 < audio_id <= 9999:
        raise ValueError(f"Ansagen-ID {audio_id} außerhalb 1..9999")

    with conn.cursor() as cur:
        cur.execute("SELECT filename FROM audio_files WHERE id = %s", (audio_id,))
        row = cur.fetchone()
    if not row:
        raise ValueError(f"Ansage {audio_id} existiert nicht")

    src = os.path.join(AUDIO_SRC_DIR, os.path.basename(row[0]))
    if not os.path.isfile(src):
        raise FileNotFoundError(f"Quelldatei fehlt: {src}")

    os.makedirs(AUDIO_DEVICE_DIR, exist_ok=True)
    dst = os.path.join(AUDIO_DEVICE_DIR, f"{audio_id:04d}.wav")

    if not os.path.isfile(dst) or os.path.getmtime(dst) < os.path.getmtime(src):
        tmp = dst + ".tmp.wav"
        result = subprocess.run(
            ["ffmpeg", "-hide_banner", "-loglevel", "error", "-y", "-i", src,
             "-ac", "1", "-ar", str(AUDIO_RATE), "-acodec", "pcm_s16le", "-f", "wav", tmp],
            capture_output=True, text=True, timeout=120,
        )
        if result.returncode != 0 or not os.path.isfile(tmp):
            raise RuntimeError(f"ffmpeg fehlgeschlagen: {result.stderr.strip()[:200]}")
        os.replace(tmp, dst)
        log.info("Ansage %d für Controller exportiert: %s", audio_id, dst)

    with wave.open(dst, "rb") as w:
        duration_ms = round(w.getnframes() * 1000 / w.getframerate())

    return os.path.basename(dst), duration_ms


def plan_controllers(conn, measure_id: int) -> tuple[dict, list[str]]:
    """
    Gibt ({controller_id: {"device_id": ..., "pins": [...]}}, [Hinweise]) zurück.
    Hinweise nennen Haltestellen, die nicht ausgegeben werden können.
    """
    with conn.cursor(pymysql.cursors.DictCursor) as cur:
        cur.execute(
            """
            SELECT mt.station_id, s.name AS station_name,
                   c.id AS controller_id, c.device_id, c.transport,
                   c.enabled, cs.output_pin
            FROM measure_targets mt
            JOIN stations s ON s.id = mt.station_id
            LEFT JOIN controller_stations cs ON cs.station_id = mt.station_id
            LEFT JOIN controllers c ON c.id = cs.controller_id
            WHERE mt.measure_id = %s
            """,
            (measure_id,),
        )
        rows = cur.fetchall()

    controllers: dict[int, dict] = {}
    mapped: set[int] = set()
    reasons: dict[int, str] = {}
    names: dict[int, str] = {}

    for row in rows:
        sid = int(row["station_id"])
        names[sid] = row["station_name"]

        if row["controller_id"] is None:
            reasons[sid] = "kein Controller"
            continue
        if int(row["enabled"]) != 1:
            reasons[sid] = "Controller deaktiviert"
            continue
        if row["transport"] != "lan":
            reasons[sid] = "RS485 noch nicht unterstützt"
            continue
        if row["output_pin"] is None:
            reasons[sid] = "kein Ausgang zugeordnet"
            continue

        cid = int(row["controller_id"])
        entry = controllers.setdefault(cid, {"device_id": row["device_id"], "pins": set()})
        entry["pins"].add(int(row["output_pin"]))
        mapped.add(sid)

    notes = [f"{names[s]} ({reasons.get(s, 'unbekannt')})" for s in names if s not in mapped]

    result = {
        cid: {"device_id": e["device_id"], "pins": sorted(e["pins"])}
        for cid, e in controllers.items()
    }
    return result, notes


def fire(conn, measure: dict, slot: datetime, now: datetime) -> None:
    """Löst einen Termin aus. Doppelte Auslösung ist durch measure_runs ausgeschlossen."""
    if measure["repeat_type"] != "once" and measure["disable_night"] and in_night(now):
        return

    with conn.cursor() as cur:
        cur.execute(
            "INSERT IGNORE INTO measure_runs (measure_id, slot_at) VALUES (%s, %s)",
            (measure["id"], slot),
        )
        if cur.rowcount != 1:
            return
        run_id = cur.lastrowid

    detail_parts: list[str] = []
    try:
        audio_file, duration_ms = ensure_device_audio(conn, int(measure["audio_id"]))
        url = f"{AUDIO_BASE_URL}/device/{audio_file}"
        controllers, notes = plan_controllers(conn, measure["id"])

        queued = 0
        with conn.cursor() as cur:
            for cid, info in controllers.items():
                payload = {
                    "audio": int(measure["audio_id"]),
                    "pins": info["pins"],
                    "duration_ms": duration_ms,
                    "max_ms": duration_ms + MAX_MS_MARGIN,
                    "url": url,
                }
                cur.execute(
                    "INSERT INTO device_commands (controller_id, command, payload) VALUES (%s, 'play', %s)",
                    (cid, json.dumps(payload)),
                )
                queued += 1
                detail_parts.append(f"{info['device_id']}: Ausgänge {info['pins']}")

        if queued == 0:
            detail_parts.append("kein Controller ausgelöst")
        if notes:
            detail_parts.append("nicht ausgegeben: " + "; ".join(notes))

    except Exception as exc:  # noqa: BLE001 - Fehler im Protokoll festhalten
        log.exception("Maßnahme %s, Termin %s fehlgeschlagen", measure["id"], slot)
        detail_parts.append(f"Fehler: {exc}")

    with conn.cursor() as cur:
        cur.execute(
            "UPDATE measure_runs SET detail = %s WHERE id = %s",
            ("; ".join(detail_parts)[:2000], run_id),
        )

    log.info("Maßnahme %s ausgelöst (%s): %s", measure["id"], slot, "; ".join(detail_parts))


def tick(conn) -> None:
    now = now_local()
    with conn.cursor(pymysql.cursors.DictCursor) as cur:
        cur.execute(
            "SELECT id, audio_id, start_at, end_at, repeat_type, repeat_interval, disable_night "
            "FROM measures WHERE active = 1"
        )
        measures = cur.fetchall()

    for m in measures:
        try:
            slot = due_slot(m, now)
        except ValueError:
            log.warning("Maßnahme %s: Zeitangabe ungültig", m["id"])
            continue
        if slot is not None:
            fire(conn, m, slot, now)


def run_scheduler(connect, stop) -> None:
    """Läuft als eigener Thread. connect() liefert eine eigene Datenbankverbindung."""
    conn = None
    while not stop.is_set():
        try:
            if conn is None:
                conn = connect()
            else:
                conn.ping(reconnect=True)
            tick(conn)
        except pymysql.MySQLError:
            log.exception("Datenbankfehler im Scheduler, neuer Versuch")
            conn = None
        except Exception:  # noqa: BLE001
            log.exception("Unerwarteter Fehler im Scheduler")
        stop.wait(SCHED_INTERVAL_S)
