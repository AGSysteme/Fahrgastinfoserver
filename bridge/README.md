# ELA-Bridge

Verbindet den MQTT-Broker (Mosquitto) mit der Datenbank.

**Stufe 1:** Status-Meldungen der Controller empfangen und `controllers.last_seen` setzen.
Befehle für Maßnahmen folgen in einem späteren Schritt.

Topic-Schema: `ela/<device_id>/status` (retained, Last-Will `offline`), `ela/<device_id>/cmd`,
`ela/<device_id>/stop`, `ela/<device_id>/ack`. Status-Nachrichten sind JSON, z. B. `{"state": "online"}`.

Alle Zugangsdaten liegen außerhalb des Repos in `/etc/ela-bridge/bridge.env` und
`/etc/mosquitto/ela_passwd`.

## Einrichtung (einmalig, mit root)

### 1. Systembenutzer
```bash
sudo useradd --system --no-create-home --shell /usr/sbin/nologin ela-bridge
```

### 2. Datenbankbenutzer mit minimalen Rechten
```bash
sudo mariadb -e "
CREATE USER 'ela_bridge'@'localhost' IDENTIFIED BY 'PASSWORT';
CREATE USER 'ela_bridge'@'127.0.0.1' IDENTIFIED BY 'PASSWORT';
GRANT SELECT, UPDATE ON ela.controllers TO 'ela_bridge'@'localhost';
GRANT SELECT, UPDATE ON ela.controllers TO 'ela_bridge'@'127.0.0.1';
FLUSH PRIVILEGES;"
```

### 3. Konfiguration
```bash
sudo mkdir -p /etc/ela-bridge
sudo cp /opt/ela/app/bridge/bridge.env.example /etc/ela-bridge/bridge.env
sudo nano /etc/ela-bridge/bridge.env          # Passwörter eintragen
sudo chown root:ela-bridge /etc/ela-bridge/bridge.env
sudo chmod 640 /etc/ela-bridge/bridge.env
```

### 4. Python-Umgebung
```bash
cd /opt/ela/app/bridge
python3 -m venv venv
venv/bin/pip install -r requirements.txt
```

### 5. Mosquitto
```bash
sudo cp /opt/ela/app/bridge/mosquitto/ela.conf /etc/mosquitto/conf.d/ela.conf
sudo cp /opt/ela/app/bridge/mosquitto/ela_acl.example /etc/mosquitto/ela_acl
sudo mosquitto_passwd -c /etc/mosquitto/ela_passwd ela-bridge
sudo chown root:mosquitto /etc/mosquitto/ela_passwd /etc/mosquitto/ela_acl
sudo chmod 640 /etc/mosquitto/ela_passwd /etc/mosquitto/ela_acl
sudo systemctl restart mosquitto
```
Das Passwort von `ela-bridge` muss dasselbe sein wie `ELA_MQTT_PASSWORD` in `bridge.env`.

### 6. Dienst
```bash
sudo cp /opt/ela/app/bridge/ela-bridge.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now ela-bridge
journalctl -u ela-bridge -f
```

## Test ohne Controller

Mit einer vorhandenen Geräte-ID (aus der Controller-Liste):
```bash
mosquitto_pub -h 127.0.0.1 -u ela-bridge -P 'MQTT-PASSWORT' \
  -t 'ela/GERAETE-ID/status' -m '{"state":"online"}'
```
Danach im Log `last_seen aktualisiert` (Debug) bzw. keine Fehlermeldung, und
`SELECT name, last_seen FROM controllers;` zeigt den neuen Zeitstempel.

## Offen

- TLS für Mosquitto (später)
- Befehle für Maßnahmen (`cmd`, `stop`) auf Basis von `Ela\Dispatch`
- Quittungen (`ack`) verarbeiten
