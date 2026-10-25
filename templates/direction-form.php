<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
<?= $direction ? 'Richtung bearbeiten' : 'Neue Richtung' ?>
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
    max-width: 800px;
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

label {
    font-weight: 600;
}

input {
    width: 100%;
    padding: 10px;

    margin-top: 6px;

    border: 1px solid #d1d5db;
    border-radius: 8px;

    font-size: 16px;
}

input[type="checkbox"] {
    width: auto;
}

.field {
    margin-bottom: 20px;
}

.actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

button,
.button {
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

.button-secondary {
    background: #6b7280;
}

.error {
    background: #fee2e2;
    color: #991b1b;

    padding: 12px;

    margin-bottom: 20px;

    border-radius: 8px;
}

</style>

</head>

<body>

<header>

<h1>ELA Server</h1>

</header>

<main>

<div class="card">

<h2>
<?= $direction
    ? 'Richtung bearbeiten'
    : 'Neue Richtung'
?>
</h2>

<p>

Linie
<strong>
<?= htmlspecialchars((string) $line['number']) ?>
</strong>

–

<?= htmlspecialchars((string) ($line['name'] ?? '')) ?>

</p>

<?php if ($error !== null): ?>

<div class="error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

<form method="post">

<input
    type="hidden"
    name="page"
    value="lines"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<?php if ($direction): ?>

<input
    type="hidden"
    name="action"
    value="direction-update"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $direction['id'] ?>"
>

<?php else: ?>

<input
    type="hidden"
    name="action"
    value="direction-create"
>

<input
    type="hidden"
    name="line_id"
    value="<?= (int) $line['id'] ?>"
>

<?php endif; ?>


<div class="field">

<label for="name">
Bezeichnung
</label>

<input
    id="name"
    type="text"
    name="name"
    required
    maxlength="200"
    value="<?= htmlspecialchars(
        (string) ($direction['name'] ?? '')
    ) ?>"
>

</div>


<div class="field">

<label for="destination">
Ziel
</label>

<input
    id="destination"
    type="text"
    name="destination"
    maxlength="200"
    value="<?= htmlspecialchars(
        (string) ($direction['destination'] ?? '')
    ) ?>"
>

</div>


<?php if ($direction): ?>

<div class="field">

<label>

<input
    type="checkbox"
    name="active"
    value="1"
    <?= (int) $direction['active'] === 1
        ? 'checked'
        : '' ?>
>

Aktiv

</label>

</div>

<?php endif; ?>


<div class="actions">

<button type="submit">
Speichern
</button>

<a
    class="button button-secondary"
    href="/?page=lines&action=directions&id=<?= (int) $line['id'] ?>"
>
Abbrechen
</a>

</div>

</form>

</div>

</main>

</body>

</html>
