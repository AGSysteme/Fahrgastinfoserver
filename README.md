# Fahrgastinfoserver
Ein Server für akustische und dynamische Fahrgastinformation

## Struktur

- `public/index.php` – Front Controller: wählt die Seite, prüft CSRF bei POST und ruft die Seiten-Klasse auf.
- `src/Web/` – Seiten-Controller (eine Klasse pro Bereich: Haltestellen, Linien, Controller, Ansagen, Maßnahmen, Maßnahmen-Wizard, Dashboard) sowie Hilfsklassen für Ansicht, Zeitplan-Prüfung und Service-Bündel.
- `src/` – Repositories für die Datenbank (Stations, Lines, Controllers, Audio, Measures) sowie Csrf, Router, Database und Config.
- `src/Config.php` – zentrale Pfade (Datenbank, Ansagen, Piper-Modelle). Jeder Wert ist per Umgebungsvariable überschreibbar (`ELA_DB_PATH`, `ELA_AUDIO_DIR`, `ELA_MODELS_DIR`, `ELA_PIPER_BIN`).
- `templates/` – Ansichten. `templates/partials/layout-start.php` enthält den gemeinsamen Kopf (Bootstrap, Titel, Header mit Link zum Dashboard).
