<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
<?= $station
    ? 'Haltestelle bearbeiten'
    : 'Neue Haltestelle'
?>
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
}

main {
    max-width: 850px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;

    border-radius: 12px;

    padding: 28px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, .08);
}

.topbar {
    margin-bottom: 25px;
}

.topbar h2 {
    margin: 0 0 6px 0;
}

.subtitle {
    color: #6b7280;
}

.error {
    background: #fee2e2;

    color: #991b1b;

    padding: 14px 16px;

    margin-bottom: 22px;

    border-radius: 9px;

    border: 1px solid #fecaca;
}

.form-grid {
    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;
}

.form-group {
    display: flex;

    flex-direction: column;

    gap: 7px;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    font-weight: 600;

    color: #374151;
}

input[type="text"],
input[type="number"] {
    width: 100%;

    padding: 11px 13px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    font-size: 15px;

    background: white;
}

input:focus {
    outline: none;

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, .12);
}

.help {
    color: #6b7280;

    font-size: 13px;
}

.checkbox {
    display: flex;

    align-items: center;

    gap: 9px;

    padding: 14px;

    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 8px;
}

.checkbox input {
    width: 18px;
    height: 18px;
}

.actions {
    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    margin-top: 28px;

    padding-top: 22px;

    border-top: 1px solid #e5e7eb;
}

.button {
    display: inline-block;

    padding: 10px 18px;

    border: 0;

    border-radius: 8px;

    background: #2563eb;

    color: white;

    text-decoration: none;

    font-weight: 600;

    cursor: pointer;

    font-size: 15px;
}

.button:hover {
    background: #1d4ed8;
}

.button-secondary {
    background: #e5e7eb;

    color: #374151;
}

.button-secondary:hover {
    background: #d1d5db;
}

@media (max-width: 700px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }

    .actions {
        align-items: stretch;

        flex-direction: column-reverse;
    }

    .actions .button {
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

<?= $station
    ? 'Haltestelle bearbeiten'
    : 'Neue Haltestelle'
?>

</h2>

<div class="subtitle">

<?php if ($station): ?>

Haltestellendaten bearbeiten

<?php else: ?>

Neue Haltestelle im System anlegen

<?php endif; ?>

</div>

</div>


<?php if ($error !== null): ?>

<div class="error">

<?= htmlspecialchars(
    (string) $error
) ?>

</div>

<?php endif; ?>


<form method="post">

<input
    type="hidden"
    name="page"
    value="stations"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<input
    type="hidden"
    name="action"
    value="<?= $station ? 'update' : 'create' ?>"
>

<?php if ($station): ?>

<input
    type="hidden"
    name="id"
    value="<?= (int) $station['id'] ?>"
>

<?php endif; ?>


<div class="form-grid">


<div class="form-group full">

<label for="name">
Name
</label>

<input
    id="name"
    type="text"
    name="name"
    required
    maxlength="200"
    value="<?= htmlspecialchars(
        (string) ($station['name'] ?? '')
    ) ?>"
    placeholder="z. B. Hauptbahnhof"
>

</div>


<div class="form-group">

<label for="short_name">
Kurzname
</label>

<input
    id="short_name"
    type="text"
    name="short_name"
    maxlength="100"
    value="<?= htmlspecialchars(
        (string) ($station['short_name'] ?? '')
    ) ?>"
    placeholder="z. B. Hbf"
>

<div class="help">
Kurze Bezeichnung für Anzeigen.
</div>

</div>


<div class="form-group">

<label for="station_code">
Haltestellencode
</label>

<input
    id="station_code"
    type="text"
    name="station_code"
    maxlength="50"
    value="<?= htmlspecialchars(
        (string) ($station['station_code'] ?? '')
    ) ?>"
    placeholder="z. B. HST001"
>

<div class="help">
Eindeutiger interner Haltestellencode.
</div>

</div>


<div class="form-group">

<label for="latitude">
Breitengrad
</label>

<input
    id="latitude"
    type="number"
    name="latitude"
    step="any"
    value="<?= htmlspecialchars(
        (string) ($station['latitude'] ?? '')
    ) ?>"
    placeholder="z. B. 48.3659"
>

<div class="help">
Geografische Position der Haltestelle.
</div>

</div>


<div class="form-group">

<label for="longitude">
Längengrad
</label>

<input
    id="longitude"
    type="number"
    name="longitude"
    step="any"
    value="<?= htmlspecialchars(
        (string) ($station['longitude'] ?? '')
    ) ?>"
    placeholder="z. B. 10.8985"
>

<div class="help">
Geografische Position der Haltestelle.
</div>

</div>


<?php if ($station): ?>

<div class="form-group full">

<label class="checkbox">

<input
    type="checkbox"
    name="active"
    value="1"
    <?= (int) $station['active'] === 1
        ? 'checked'
        : '' ?>
>

<span>
Haltestelle ist aktiv
</span>

</label>

</div>

<?php endif; ?>


</div>


<div class="actions">

<a
    class="button button-secondary"
    href="/?page=stations"
>
← Abbrechen
</a>

<button
    type="submit"
    class="button"
>
<?= $station
    ? 'Änderungen speichern'
    : 'Haltestelle anlegen'
?>
</button>

</div>

</form>

</div>

</main>

</body>

</html>
