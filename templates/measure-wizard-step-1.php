<?php

declare(strict_types=1);

$wizard =
    $_SESSION['measure_wizard']
        ?? [];

$selectedAudioId =
    !empty($wizard['audio_id'])
        ? (int) $wizard['audio_id']
        : (
            isset($_GET['audio_id'])
                ? (int) $_GET['audio_id']
                : 0
        );

$wizardName =
    (string) (
        $wizard['name']
            ?? ''
    );

?>
<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Neue Maßnahme – ELA Server</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f4f6f8;
    color: #222;
    font-family: Arial, sans-serif;
}

main {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.card {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
}

h1 {
    margin-top: 0;
}

.subtitle {
    color: #777;
    margin-bottom: 30px;
}

.field {
    margin-bottom: 22px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

input,
select {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-size: 16px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.button {
    display: inline-block;
    padding: 11px 18px;
    border: 0;
    border-radius: 7px;
    background: #1677ff;
    color: white;
    text-decoration: none;
    cursor: pointer;
    font-size: 16px;
}

.button-secondary {
    background: #777;
}

.audio-option {
    padding: 8px 0;
}

.audio-info {
    margin-top: 7px;
    color: #777;
    font-size: 14px;
}

.empty {
    padding: 20px;
    background: #f7f7f7;
    border-radius: 8px;
    color: #777;
}

</style>

</head>

<body>

<main>

<div class="card">

<h1>Neue Maßnahme</h1>

<div class="subtitle">
Schritt 1 von 2 – Maßnahme und Ansage festlegen
</div>

<?php if (!$audioFiles): ?>

<div class="empty">

Es sind noch keine Ansagen vorhanden.

Bitte zuerst eine Ansage erzeugen.

</div>

<div class="form-actions">

<a
    href="/?page=audio"
    class="button"
>
    Zu den Ansagen
</a>

</div>

<?php else: ?>

<form method="post">

<input
    type="hidden"
    name="page"
    value="measure-wizard"
>

<input
    type="hidden"
    name="action"
    value="measure-wizard-step-1"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<div class="field">

<label for="name">
Name der Maßnahme
</label>

<input
    type="text"
    id="name"
    name="name"
    required
    value="<?= htmlspecialchars($wizardName) ?>"
    placeholder="z. B. Baustelle Hauptbahnhof"
>

</div>


<div class="field">

<label for="audio_id">
Ansage
</label>

<select
    id="audio_id"
    name="audio_id"
    required
>

<option value="">
Bitte Ansage auswählen
</option>

<?php foreach ($audioFiles as $audio): ?>

<?php
$audioId =
    (int) $audio['id'];

$selected =
    $audioId === $selectedAudioId;
?>

<option
    value="<?= $audioId ?>"
    <?= $selected ? 'selected' : '' ?>
>
    <?= htmlspecialchars(
        (string) $audio['text']
    ) ?>
    –
    <?= htmlspecialchars(
        ucfirst(
            (string) $audio['voice']
        )
    ) ?>
</option>

<?php endforeach; ?>

</select>

<div class="audio-info">
Eine Maßnahme kann genau einer Ansage zugeordnet werden.
</div>

</div>


<div class="form-actions">

<a
    href="/?page=measures"
    class="button button-secondary"
>
    Abbrechen
</a>

<button
    type="submit"
    class="button"
>
    Weiter →
</button>

</div>

</form>

<?php endif; ?>

</div>

</main>

</body>

</html>

