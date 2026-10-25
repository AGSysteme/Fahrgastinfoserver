<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Controller – ELA Server</title>

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
    max-width: 1100px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;
    border-radius: 14px;
    padding: 25px;

    box-shadow:
        0 2px 10px rgba(0, 0, 0, .08);
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;
    margin-bottom: 25px;
}

.topbar h2 {
    margin: 0 0 6px 0;
    font-size: 24px;
}

.info {
    color: #6b7280;
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

.searchbar {
    display: flex;
    gap: 10px;

    margin-bottom: 25px;
}

.searchbar input {
    flex: 1;

    min-width: 0;

    padding: 11px 13px;

    border: 1px solid #d1d5db;
    border-radius: 8px;

    font-size: 15px;
}

.searchbar input:focus {
    outline: none;
    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, .12);
}

.controllers {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 18px;
}

.controller {
    border: 1px solid #e5e7eb;

    border-radius: 12px;

    padding: 20px;

    background: #f9fafb;

    transition:
        box-shadow .15s ease,
        border-color .15s ease;
}

.controller:hover {
    border-color: #cbd5e1;

    box-shadow:
        0 4px 12px rgba(0, 0, 0, .06);
}

.controller-header {
    display: flex;

    justify-content: space-between;
    align-items: flex-start;

    gap: 15px;

    margin-bottom: 18px;
}

.controller-name {
    font-size: 20px;
    font-weight: 700;
}

.status {
    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 5px 9px;

    border-radius: 999px;

    font-size: 12px;

    font-weight: 700;

    white-space: nowrap;
}

.status.active {
    background: #dcfce7;
    color: #166534;
}

.status.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.status-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: currentColor;
}

.details {
    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 12px;

    margin-bottom: 20px;
}

.detail {
    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    padding: 10px 12px;

    min-width: 0;
}

.detail-label {
    color: #6b7280;

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: .04em;

    margin-bottom: 4px;
}

.detail-value {
    font-size: 14px;

    font-weight: 600;

    overflow-wrap: anywhere;
}

.controller-actions {
    display: flex;

    gap: 8px;

    flex-wrap: wrap;
}

.empty {
    padding: 40px 25px;

    text-align: center;

    color: #6b7280;

    border: 2px dashed #d1d5db;

    border-radius: 10px;
}

@media (max-width: 750px) {

    .controllers {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 600px) {

    main {
        margin: 20px auto;
        padding: 0 12px;
    }

    .card {
        padding: 18px;
    }

    .topbar {
        align-items: stretch;
        flex-direction: column;
    }

    .searchbar {
        flex-direction: column;
    }

    .details {
        grid-template-columns: 1fr;
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

<div>

<h2>Controller</h2>

<div class="info">
Verwaltung der angeschlossenen ELA-Controller
</div>

</div>

<a
    class="button"
    href="/?page=controllers&action=new"
>
+ Neuer Controller
</a>

</div>


<form
    method="get"
    class="searchbar"
>

<input
    type="hidden"
    name="page"
    value="controllers"
>

<input
    type="text"
    name="search"
    value="<?= htmlspecialchars($search) ?>"
    placeholder="Controller suchen..."
>

<button
    type="submit"
    class="button"
>
Suchen
</button>

<?php if ($search !== ''): ?>

<a
    href="/?page=controllers"
    class="button secondary"
>
Zurücksetzen
</a>

<?php endif; ?>

</form>

<?php if ($error !== null): ?>

<div
    style="
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 13px 15px;
        margin-bottom: 22px;
        border-radius: 9px;
    "
>
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<?php if (!$controllers): ?>

<div class="empty">

<?php if ($search !== ''): ?>

Keine Controller für
<strong>
„<?= htmlspecialchars($search) ?>“
</strong>
gefunden.

<?php else: ?>

Noch kein Controller angelegt.

<?php endif; ?>

</div>

<?php else: ?>

<div class="controllers">

<?php foreach ($controllers as $controller): ?>

<div class="controller">

<div class="controller-header">

<div class="controller-name">

<?= htmlspecialchars(
    (string) $controller['name']
) ?>

</div>

<?php if ((int) $controller['enabled'] === 1): ?>

<div class="status active">
<span class="status-dot"></span>
Aktiv
</div>

<?php else: ?>

<div class="status inactive">
<span class="status-dot"></span>
Inaktiv
</div>

<?php endif; ?>

</div>


<div class="details">

<div class="detail">

<div class="detail-label">
Geräte-ID
</div>

<div class="detail-value">
<?= htmlspecialchars(
    (string) $controller['device_id']
) ?>
</div>

</div>


<div class="detail">

<div class="detail-label">
IP-Adresse
</div>

<div class="detail-value">

<?= htmlspecialchars(
    (string) (
        $controller['ip_address'] ?? '–'
    )
) ?>

</div>

</div>


<div class="detail">

<div class="detail-label">
MQTT Client-ID
</div>

<div class="detail-value">

<?= htmlspecialchars(
    (string) $controller['mqtt_client_id']
) ?>

</div>

</div>


<div class="detail">

<div class="detail-label">
MQTT Topic
</div>

<div class="detail-value">

<?= htmlspecialchars(
    (string) $controller['mqtt_topic']
) ?>

</div>

</div>

</div>

<div class="controller-actions">

<a
    class="button"
    href="/?page=controllers&action=stations&id=<?= (int) $controller['id'] ?>"
>
Haltestellen
</a>

<a
    class="button secondary"
    href="/?page=controllers&action=edit&id=<?= (int) $controller['id'] ?>"
>
Bearbeiten
</a>

<form
    method="post"
    style="display: inline;"
    onsubmit="return confirm(
        'Controller „<?= htmlspecialchars(
            (string) $controller['name'],
            ENT_QUOTES
        ) ?>“ wirklich löschen?'
    );"
>

<input
    type="hidden"
    name="page"
    value="controllers"
>

<input
    type="hidden"
    name="action"
    value="delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $controller['id'] ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<button
    type="submit"
    class="button"
    style="
        background: #dc2626;
    "
>
Löschen
</button>

</form>

</div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div>

<p style="margin-top: 25px;">

<a href="/">
← Zurück zum Dashboard
</a>

</p>

</main>

</body>

</html>
