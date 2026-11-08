# TODO

Offene Punkte, die bewusst aufgeschoben wurden. Bei Erledigung abhaken und in der Commit-Nachricht erwähnen.

## MQTT / Bridge
- [ ] **TLS für Mosquitto einrichten** (aufgeschoben, bewusst). Derzeit unverschlüsselt im LAN, mit Benutzer und Passwort.
- [x] Bridge einrichten und starten (siehe `bridge/README.md`): Systembenutzer, DB-Benutzer `ela_bridge`, `/etc/ela-bridge/bridge.env`, Mosquitto-Passwortdatei, systemd-Dienst. Stufe 1 getestet: Status-Meldung setzt `last_seen` (Controller 2).
- [ ] Mosquitto- und DB-Passwörter erneuern, da Teile davon im Chat standen. Danach `bridge.env` und `/etc/mosquitto/ela_passwd` anpassen.
- [x] Bridge Stufe 2: Befehle (`cmd`, `stop`) aus der Tabelle `device_commands` senden, Quittungen (`ack`) eintragen. Test-Button je Controller. Protokoll in `docs/mqtt.md`.
- [ ] Automatische Auslösung von Maßnahmen: `Ela\Dispatch::planFor()` in Befehle übersetzen (bisher nur Test-Ansage).
- [ ] Timeout für Befehle, die `sent` sind und nie quittiert werden.
- [ ] Bridge: Test mit einem echten Controller (ESP32, LAN) nach Firmware-Stand.

## Controller
- [ ] ESP32-Firmware für LAN-Controller (MQTT, SD-Karte, Ausgänge 1–16 mit Watchdog).
- [ ] RS485-Anbindung über UDS2100: eigenes Frame-Protokoll festlegen (Adresse, Befehl, Länge, Daten, CRC).
- [ ] Ausgänge: Pegel-Logik am AGE (aktiv = GND, inaktiv = 24 V) mit Optokoppler-Schaltung dokumentieren.

## Oberfläche
- [ ] Controller-Liste: Online-Anzeige aus `last_seen`, Offline-Zähler im Dashboard.
- [ ] Nicht zuordenbare Zielhaltestellen einer Maßnahme anzeigen (aus `Ela\Dispatch::planFor()`), z. B. im Wizard.
- [ ] Button „Test abspielen“ je Controller (nach Bridge Stufe 2).

## Datenbank und Betrieb
- [ ] Migrationen 001 und 002 auf der produktiven Datenbank einspielen, falls noch nicht geschehen (`data/migrations/`).
- [ ] Datenbank-Passwort aus `/etc/php/8.4/fpm/pool.d/www.conf` in eine eigene Datei auslagern (`ela-db.conf`, Rechte 640, root).
- [ ] Backup-Skript für MariaDB (`mariadb-dump`, täglich per Cron, Aufbewahrung festlegen).
- [ ] Zeitzonen: Timestamps bleiben UTC, Zeitpläne sind Berlin-Wandzeit. Bei neuen Zeitfeldern beachten (siehe Kommentar in `data/schema-mariadb.sql`).

## Qualität
- [ ] Browsertest der Seiten: Wizard Schritt 2 (Suche, Drag & Drop), COM-Server, Controller-Formular, Haltestellen-Zuordnung mit Ausgängen.
- [ ] Automatische Tests für `Ela\Dispatch` und die Pin-Prüfung.
