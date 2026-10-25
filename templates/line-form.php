<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
<?= $line ? 'Linie bearbeiten' : 'Neue Linie' ?>
- ELA Server
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
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
}

input,
textarea {
    width: 100%;

    padding: 10px;

    border:
        1px solid #d1d5db;

    border-radius: 8px;

    font: inherit;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.form-group {
    margin-bottom: 20px;
}

button,
a.button {
    display: inline-block;

    padding: 10px 16px;

    border: 0;
    border-radius: 8px;

    background: #2563eb;
    color: white;

    text-decoration: none;

    font-weight: 600;

    cursor: pointer;
}

.button-secondary {
    background: #6b7280 !important;
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
<?= $line
    ? 'Linie bearbeiten'
    : 'Neue Linie'
?>
</h2>

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
    name="action"
    value="<?= $line ? 'update' : 'create' ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<?php if ($line): ?>

<input
    type="hidden"
    name="id"
    value="<?= (int) $line['id'] ?>"
>

<?php endif; ?>

<div class="form-group">

<label for="number">
Liniennummer *
</label>

<input
    id="number"
    type="text"
    name="number"
    maxlength="50"
    required
    value="<?= htmlspecialchars(
        (string) ($line['number'] ?? '')
    ) ?>"
>

</div>

<div class="form-group">

<label for="name">
Name
</label>

<input
    id="name"
    type="text"
    name="name"
    maxlength="200"
    placeholder="z. B. Linie 18"
    value="<?= htmlspecialchars(
        (string) ($line['name'] ?? '')
    ) ?>"
>

</div>

<div class="form-group">

<label for="description">
Beschreibung
</label>

<textarea
    id="description"
    name="description"
><?= htmlspecialchars(
    (string) ($line['description'] ?? '')
) ?></textarea>

</div>

<?php if ($line): ?>

<div class="form-group">

<label>

<input
    type="checkbox"
    name="active"
    value="1"
    <?= (int) $line['active'] === 1
        ? 'checked'
        : '' ?>
>

 Aktiv

</label>

</div>

<?php endif; ?>

<button type="submit">
    Speichern
</button>

<a
    class="button button-secondary"
    href="/?page=lines"
>
    Abbrechen
</a>

</form>

</div>

</main>

</body>

</html>
