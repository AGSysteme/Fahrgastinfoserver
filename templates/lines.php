<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Linien - ELA Server</title>

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
    max-width: 1200px;
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

a.button,
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

a.button:hover,
button:hover {
    background: #1d4ed8;
}

.button-secondary {
    background: #6b7280 !important;
}

.button-danger {
    background: #dc2626 !important;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    text-align: left;
    padding: 12px;

    border-bottom:
        1px solid #e5e7eb;
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
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

<h1>ELA Server – Linien</h1>

</header>

<main>

<div class="card">

<div class="topbar">

<h2>Linien</h2>

<a
    class="button"
    href="/?page=lines&action=new"
>
    + Neue Linie
</a>

</div>

<?php if ($error !== null): ?>

<div class="error">
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<?php if (!$lines): ?>

<p>
    Es sind noch keine Linien vorhanden.
</p>

<?php else: ?>

<table>

<thead>

<tr>

<th>Nummer</th>
<th>Name</th>
<th>Beschreibung</th>
<th>Status</th>
<th>Aktionen</th>

</tr>

</thead>

<tbody>

<?php foreach ($lines as $line): ?>

<tr>

<td>
    <strong>
        <?= htmlspecialchars(
            (string) $line['number']
        ) ?>
    </strong>
</td>

<td>
    <?= htmlspecialchars(
        (string) ($line['name'] ?? '')
    ) ?>
</td>

<td>
    <?= htmlspecialchars(
        (string) ($line['description'] ?? '')
    ) ?>
</td>

<td>

<?php if ((int) $line['active'] === 1): ?>

<span class="active">
    Aktiv
</span>

<?php else: ?>

<span class="inactive">
    Inaktiv
</span>

<?php endif; ?>

</td>

<td>

<div class="actions">

<a
    class="button"
    href="/?page=lines&action=edit&id=<?= (int) $line['id'] ?>"
>
    Bearbeiten
</a>

<a
    class="button button-secondary"
    href="/?page=lines&action=stations&id=<?= (int) $line['id'] ?>"
>
    Haltestellen
</a>

<form
    method="post"
    onsubmit="return confirm('Diese Linie wirklich löschen?');"
>

<input
    type="hidden"
    name="page"
    value="lines"
>

<input
    type="hidden"
    name="action"
    value="delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $line['id'] ?>"
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

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

<p style="margin-top: 25px;">

<a href="/">
    ← Zum Dashboard
</a>

</p>

</div>

</main>

</body>

</html>
