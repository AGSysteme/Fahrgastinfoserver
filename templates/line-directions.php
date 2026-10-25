<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Richtungen – <?= htmlspecialchars((string) $line['number']) ?>
</title>

<style>

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

main {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;
    border-radius: 12px;
    padding: 24px;

    box-shadow:
        0 2px 8px rgba(0,0,0,.08);
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;

    margin-bottom: 20px;
}

.direction {
    border: 1px solid #e5e7eb;
    border-radius: 10px;

    padding: 18px;

    margin-bottom: 15px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;
}

.direction-info strong {
    display: block;
    font-size: 18px;
}

.direction-info span {
    color: #6b7280;
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.button,
button {
    display: inline-block;

    border: 0;
    border-radius: 8px;

    padding: 10px 16px;

    background: #2563eb;
    color: white;

    text-decoration: none;

    font-weight: 600;

    cursor: pointer;
}

.button:hover,
button:hover {
    background: #1d4ed8;
}

.button-secondary {
    background: #6b7280 !important;
}

.button-danger {
    background: #dc2626 !important;
}

.error {
    background: #fee2e2;
    color: #991b1b;

    padding: 12px;

    margin-bottom: 20px;

    border-radius: 8px;
}

.active {
    color: #15803d;
    font-weight: 600;
}

.inactive {
    color: #b91c1c;
    font-weight: 600;
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

<h2>
Linie <?= htmlspecialchars((string) $line['number']) ?>
</h2>

<p>
<?= htmlspecialchars((string) ($line['name'] ?? '')) ?>
</p>

</div>

<a
    class="button"
    href="/?page=lines&action=direction-new&id=<?= (int) $line['id'] ?>"
>
    + Neue Richtung
</a>

</div>

<?php if ($error !== null): ?>

<div class="error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

<?php if (!$directions): ?>

<p>
Für diese Linie wurden noch keine Richtungen angelegt.
</p>

<?php else: ?>

<?php foreach ($directions as $direction): ?>

<div class="direction">

<div class="direction-info">

<strong>
<?= htmlspecialchars(
    (string) $direction['name']
) ?>
</strong>

<span>
Ziel:
<?= htmlspecialchars(
    (string) $direction['destination']
) ?>
</span>

<br>

<?php if ((int) $direction['active'] === 1): ?>

<span class="active">
Aktiv
</span>

<?php else: ?>

<span class="inactive">
Inaktiv
</span>

<?php endif; ?>

</div>

<div class="actions">

<a
    class="button"
    href="/?page=lines&action=direction-edit&id=<?= (int) $direction['id'] ?>"
>
    Bearbeiten
</a>

<a
    class="button button-secondary"
    href="/?page=lines&action=direction-stations&id=<?= (int) $direction['id'] ?>"
>
    Haltestellen
</a>

<form
    method="post"
    onsubmit="return confirm('Diese Richtung wirklich löschen?');"
>

<input
    type="hidden"
    name="page"
    value="lines"
>

<input
    type="hidden"
    name="action"
    value="direction-delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $direction['id'] ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<button
    type="submit"
    class="button-danger"
>
    Löschen
</button>

</form>

</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

<p style="margin-top: 25px;">

<a href="/?page=lines">
← Zurück zu den Linien
</a>

</p>

</div>

</main>

</body>

</html>
