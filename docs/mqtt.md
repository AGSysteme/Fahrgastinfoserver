# MQTT-Vertrag: Server ↔ Controller

Dieses Dokument legt fest, wie Server (Bridge) und Controller (ESP32) miteinander sprechen.
Es gilt für LAN-Controller. RS485-Controller über den UDS2100 bekommen dieselben Inhalte
in einem eigenen Frame-Format (noch offen).

## Topics

| Topic | Richtung | Retained | Inhalt |
|---|---|---|---|
| `ela/<device_id>/status` | Controller → Server | ja | Heartbeat `{"state":"online"}`. Last-Will: `{"state":"offline"}` |
| `ela/<device_id>/cmd` | Server → Controller | nein | Wiedergabe-Befehl |
| `ela/<device_id>/stop` | Server → Controller | nein | sofortiger Stopp |
| `ela/<device_id>/ack` | Controller → Server | nein | Quittung zu `cmd` oder `stop` |

Alle Nachrichten sind JSON in UTF-8, QoS 1.

`<device_id>` ist die Geräte-ID aus der Controller-Verwaltung.

## Befehl `cmd` (Wiedergabe)

```json
{
  "id": 118,
  "audio": 0,
  "pins": [3, 4],
  "duration_ms": 6107,
  "max_ms": 11107
}
```

| Feld | Bedeutung |
|---|---|
| `id` | Befehls-ID aus der Datenbank (`device_commands.id`), wird in der Quittung zurückgegeben |
| `audio` | Ansagen-ID. Die Datei liegt auf der SD-Karte als `0000.wav` bis `9999.wav` (4-stellig) |
| `pins` | Ausgänge 1–16, die für die Dauer der Ansage aktiviert werden. Leer = nur Audio |
| `duration_ms` | Länge der Ansage in Millisekunden |
| `max_ms` | Sicherheitsgrenze: `duration_ms` + 5000 ms |

**Ablauf im Controller:**
1. Pins in `pins` aktivieren (AGE-Leitung auf GND ziehen).
2. Datei `<audio>.wav` von der SD-Karte abspielen.
3. Nach Ende der Datei (spätestens nach `duration_ms`) alle Pins wieder freigeben.
4. Nach `max_ms` in jedem Fall alle Pins freigeben und die Wiedergabe beenden (Watchdog).

## Befehl `stop` (Abbruch)

```json
{ "id": 119 }
```

Sofort alle Pins freigeben und die Wiedergabe beenden. Danach Quittung mit `"state":"done"`.

## Quittung `ack`

```json
{ "id": 118, "state": "started" }
{ "id": 118, "state": "done" }
{ "id": 118, "state": "error", "detail": "SD-Datei 0000.wav nicht gefunden" }
```

| `state` | Bedeutung |
|---|---|
| `started` | Pins sind gesetzt, Wiedergabe läuft |
| `done` | Wiedergabe beendet, Pins freigegeben |
| `error` | Befehl nicht ausführbar, `detail` nennt den Grund |

## Audiodateien auf der SD-Karte

- Format: WAV, PCM 8 Bit unsigned, mono, 16 kHz (für den internen DAC des ESP32, Ausgang GPIO 25).
- Dateiname: `<audio-id als 4-stellige Zahl>.wav`, z. B. `0000.wav`.
- Die Test-Ansage hat die ID `0`. Quelle auf dem Server: `audio/system/testdurchsage.wav`.
- Das Übertragen von Ansagen auf die SD-Karte ist ein eigener Schritt (noch offen).

## Status

- `{"state":"online"}` beim Start und danach regelmäßig (Vorschlag: alle 30 s).
- Last-Will beim Verbindungsabbruch: `{"state":"offline"}`, retained.
- Der Server setzt bei `online` die Spalte `controllers.last_seen` (UTC).

## Offen

- Vorlaufzeit der Ausgänge vor dem Audiostart (AGE-Anforderung, noch zu klären)
- Wiederholung eines unbestätigten Befehls (aktuell keine automatische Wiederholung)
- Format für den RS485-Weg über den UDS2100
