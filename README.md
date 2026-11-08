# Fahrgastinfoserver
Ein Server für akustische und dynamische Fahrgastinformation

## Struktur

- `public/index.php` – Front Controller: wählt die Seite, prüft CSRF bei POST und ruft die Seiten-Klasse auf.
- `src/Web/` – Seiten-Controller (eine Klasse pro Bereich: Haltestellen, Linien, Controller, Ansagen, Maßnahmen, Maßnahmen-Wizard, Dashboard) sowie Hilfsklassen für Ansicht, Zeitplan-Prüfung und Service-Bündel.
- `src/` – Repositories für die Datenbank (Stations, Lines, Controllers, Audio, Measures) sowie Csrf, Router, Database und Config.
- `src/Config.php` – zentrale Pfade und Datenbankzugang. Jeder Wert ist per Umgebungsvariable überschreibbar (`ELA_DB_HOST`, `ELA_DB_PORT`, `ELA_DB_NAME`, `ELA_DB_USER`, `ELA_DB_PASSWORD`, `ELA_AUDIO_DIR`, `ELA_MODELS_DIR`, `ELA_PIPER_BIN`).

## Datenbank

Die App nutzt MariaDB (utf8mb4). Standardwerte: Host `127.0.0.1`, Port `3306`, Datenbank `ela`, Benutzer `ela`.

- Schema anlegen: `mariadb -u root -p ela < data/schema-mariadb.sql`
- Webserver und PHP-FPM müssen die `ELA_DB_*`-Variablen ebenfalls sehen.
- `templates/` – Ansichten. `templates/partials/layout-start.php` enthält den gemeinsamen Kopf (Bootstrap, Titel, Header mit Link zum Dashboard).
