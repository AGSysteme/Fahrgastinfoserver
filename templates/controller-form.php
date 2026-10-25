<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
<?= $controller
    ? 'Controller bearbeiten'
    : 'Neuer Controller' ?>
 – ELA Server
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    font-family:
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    background: #f3f4f6;
    color: #111827;
}

header {
    background: #111827;
    color: white;
    padding: 20px 30px;
}

header h1 {
    margin: 0;
    font-size: 24px;
}

main {
    max-width: 800px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;

    border-radius: 14px;

    padding: 28px;

    box-shadow:
        0 2px 10px rgba(0, 0, 0, .08);
}

.topbar {
    margin-bottom: 25px;
}

.topbar h2 {
    margin: 0 0 6px 0;

    font-size: 24px;
}

.info {
    color: #6b7280;
}

.error {
    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #fecaca;

    padding: 13px 15px;

    margin-bottom: 22px;

    border-radius: 9px;
}

.section {
    margin-bottom: 25px;

    padding: 20px;

    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 11px;
}

.section h3 {
    margin: 0 0 5px 0;

    font-size: 17px;
}

.section-info {
    color: #6b7280;

    font-size: 13px;

    margin-bottom: 18px;
}

.fields {
    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 18px;
}

.field.full {
    grid-column: 1 / -1;
}

.field label {
    display: block;

    font-size: 14px;

    font-weight: 700;

    margin-bottom: 7px;
}

.required {
    color: #dc2626;
}

.field input {
    width: 100%;

    padding: 11px 13px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    background: white;

    font: inherit;

    font-size: 15px;
}

.field input:focus {
    outline: none;

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, .12);
}

.field-help {
    margin-top: 5px;

    color: #6b7280;

    font-size: 12px;
}

.checkbox {
    display: flex;

    align-items: center;

    gap: 9px;

    font-weight: 600;

    cursor: pointer;
}

.checkbox input {
    width: 17px;
    height: 17px;
}

.actions {
    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 12px;

    margin-top: 25px;
}

.action-right {
    display: flex;

    gap: 8px;

    flex-wrap: wrap;
}

.button {
    display: inline-block;

    padding: 10px 16px;

    background: #2563eb;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: 600;

    border: 0;

    cursor: pointer;

    font-size: 14px;
}

.button:hover {
    background: #1d4ed8;
}

.button.secondary {
    background: #e5e7eb;

    color: #111827;
}

.button.secondary:hover {
    background: #d1d5db;
}

@media (max-width: 650px) {

    main {
        margin: 20px auto;

        padding: 0 12px;
    }

    .card {
        padding: 18px;
    }

    .fields {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: auto;
    }

    .actions {
        align-items: stretch;

        flex-direction: column-reverse;
    }

    .action-right {
        flex-direction: column;
    }

    .action-right .button {
        text-align: center;
    }

}

</style>

</head>

<body>

<header>

<h1>ELA Server</h1>

</header>

<main>

<div class="card">

<div class="topbar">

<h2>

<?= $controller
    ? 'Controller bearbeiten'
    : 'Neuer Controller' ?>

</h2>

<div class="info">

<?= $controller
    ? 'Technische Einstellungen des Controllers bearbeiten.'
    : 'Einen neuen ELA-Controller konfigurieren.' ?>

</div>

</div>


<?php if ($error !== null): ?>

<div class="error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>


<form method="post">

<input
    type="hidden"
    name="page"
    value="controllers"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<input
    type="hidden"
    name="action"
    value="<?= $controller
        ? 'update'
        : 'create' ?>"
>

<?php if ($controller): ?>

<input
    type="hidden"
    name="id"
    value="<?= (int) $controller['id'] ?>"
>

<?php endif; ?>


<div class="section">

<h3>
Allgemein
</h3>

<div class="section-info">
Grundlegende Identifikation des Controllers.
</div>

<div class="fields">

<div class="field">

<label for="name">
Name
<span class="required">*</span>
</label>

<input
    id="name"
    type="text"
    name="name"
    required
    maxlength="100"
    autocomplete="off"
    value="<?= htmlspecialchars(
        (string) (
            $controller['name'] ?? ''
        )
    ) ?>"
>

</div>


<div class="field">

<label for="device_id">
Geräte-ID
<span class="required">*</span>
</label>

<input
    id="device_id"
    type="text"
    name="device_id"
    required
    maxlength="100"
    autocomplete="off"
    value="<?= htmlspecialchars(
        (string) (
            $controller['device_id'] ?? ''
        )
    ) ?>"
>

<div class="field-help">
Eindeutige Kennung des physischen Geräts.
</div>

</div>

</div>

</div>


<div class="section">

<h3>
MQTT
</h3>

<div class="section-info">
Verbindungseinstellungen für die Kommunikation mit dem ELA-System.
</div>

<div class="fields">

<div class="field">

<label for="mqtt_client_id">
MQTT Client-ID
</label>

<input
    id="mqtt_client_id"
    type="text"
    name="mqtt_client_id"
    maxlength="200"
    autocomplete="off"
    value="<?= htmlspecialchars(
        (string) (
            $controller['mqtt_client_id'] ?? ''
        )
    ) ?>"
>

</div>


<div class="field">

<label for="mqtt_topic">
MQTT Topic
</label>

<input
    id="mqtt_topic"
    type="text"
    name="mqtt_topic"
    maxlength="200"
    autocomplete="off"
    value="<?= htmlspecialchars(
        (string) (
            $controller['mqtt_topic'] ?? ''
        )
    ) ?>"
>

</div>


<div class="field full">

<label for="ip_address">
IP-Adresse
</label>

<input
    id="ip_address"
    type="text"
    name="ip_address"
    maxlength="45"
    autocomplete="off"
    placeholder="z. B. 192.168.1.50"
    value="<?= htmlspecialchars(
        (string) (
            $controller['ip_address'] ?? ''
        )
    ) ?>"
>

<div class="field-help">
Optional. Kann leer gelassen werden.
</div>

</div>

</div>

</div>


<div class="section">

<h3>
COM-Server
</h3>

<div class="section-info">
Zuordnung des Controllers zum zuständigen ELA-COM-Server und zur RS485-Schnittstelle.
</div>

<div class="fields">

<div class="field full">

<label for="com_server_id">
COM-Server
</label>

<select
    id="com_server_id"
    name="com_server_id"
    style="
        width: 100%;
        padding: 11px 13px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: white;
        font: inherit;
        font-size: 15px;
    "
>

<option value="">
Kein COM-Server zugeordnet
</option>

<?php foreach ($comServers as $comServer): ?>

<option
    value="<?= (int) $comServer['id'] ?>"
    <?= (
        isset($controller['com_server_id']) &&
        (int) $controller['com_server_id'] ===
        (int) $comServer['id']
    )
        ? 'selected'
        : '' ?>
>

<?= htmlspecialchars(
    (string) $comServer['name']
) ?>

–
<?= htmlspecialchars(
    (string) $comServer['host']
) ?>:
<?= (int) $comServer['tcp_port'] ?>

<?php if ((int) $comServer['enabled'] !== 1): ?>
 (deaktiviert)
<?php endif; ?>

</option>

<?php endforeach; ?>

</select>

<div class="field-help">
Der Controller kann später über diesen COM-Server angesprochen werden.
</div>

</div>


<div class="field">

<label for="serial_port">
Serieller Port
</label>

<input
    id="serial_port"
    type="number"
    name="serial_port"
    min="1"
    step="1"
    value="<?= htmlspecialchars(
        (string) (
            $controller['serial_port'] ?? ''
        )
    ) ?>"
>

<div class="field-help">
RS485-/Serieller Port am COM-Server.
</div>

</div>


<div class="field">

<label for="rs485_address">
RS485-Adresse
</label>

<input
    id="rs485_address"
    type="number"
    name="rs485_address"
    min="1"
    max="247"
    step="1"
    value="<?= htmlspecialchars(
        (string) (
            $controller['rs485_address'] ?? ''
        )
    ) ?>"
>

<div class="field-help">
Modbus-/RS485-Adresse des Controllers.
</div>

</div>

</div>

</div>


<?php if ($controller): ?>

<div class="section">

<h3>
Status
</h3>

<div class="section-info">
Deaktivierte Controller bleiben im System erhalten, werden aber als inaktiv angezeigt.
</div>

<label class="checkbox">

<input
    type="checkbox"
    name="enabled"
    value="1"
    <?= (int) $controller['enabled'] === 1
        ? 'checked'
        : '' ?>
>

Controller ist aktiv

</label>

</div>

<?php endif; ?>


<div class="actions">

<a
    href="/?page=controllers"
    class="button secondary"
>
← Abbrechen
</a>

<div class="action-right">

<button
    type="submit"
    class="button"
>
<?= $controller
    ? 'Änderungen speichern'
    : 'Controller anlegen' ?>
</button>

</div>

</div>

</form>

</div>

</main>

</body>

</html>
