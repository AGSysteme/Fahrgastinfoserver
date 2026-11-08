# Fahrgastinfo Server

## Funktionen
- Audio-Dateien erzeugen über Live eingesprochen oder über TTS mit piper
- Maßnahmen erstellen/verwalten
- Linien
- Haltestellen verwalten
- Controller verwalten
- Controller ↔ Haltestellen Zuordnung

## Hardware
- ESP32 Controller
- RS485 Kommunikation
- LAN Kommunikation
  



# Einstellungen Fahrgastinfoserver

## Server

Debian 13

### Datenbank

-Mysql

| Parameter | Wert |
|------------|------|
| Host | localhost |
| Datenbank | fahrgastinfoserver |
| Zeichensatz | utf8mb4 |


## Style
-es wird lokal Bootstrap verwendet

## Audio

### Standardformat

- WAV
- Mono
- 22050 Hz
- 8 Bit

## Controller

### ESP32

- DHCP: Ja
- Firmware: 1.0
- Update per Webinterface

## Haltestellen

Jede Haltestelle besitzt:

- ID
- Name
- GPS-Koordinaten
- Zugeordnete Controller

## Maßnahmen

Maßnahmen können:

- Audio auslösen
- Anzeigen aktualisieren
- Controller schalten
