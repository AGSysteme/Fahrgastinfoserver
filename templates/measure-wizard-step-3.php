<?php

declare(strict_types=1);

$wizard =
    $_SESSION['measure_wizard']
        ?? [];

$schedule =
    $wizard['schedule']
        ?? [];

$scheduleType =
    (string) ($schedule['type'] ?? 'once');

$startAt =
    (string) ($schedule['start_at'] ?? '');

$endAt =
    (string) ($schedule['end_at'] ?? '');

$interval =
    (int) ($schedule['interval'] ?? 5);

$disableNight =
    !empty($schedule['disable_night']);

$isPeriod =
    $scheduleType === 'period';

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

    font-family:
        Arial,
        sans-serif;
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

    box-shadow:
        0 2px 10px rgba(0,0,0,.08);
}

h1 {
    margin-top: 0;
}

.subtitle {
    color: #777;

    margin-bottom: 25px;
}

.measure-info {
    background: #f7f9fc;

    border: 1px solid #e2e6ec;

    border-radius: 8px;

    padding: 14px 16px;

    margin-bottom: 25px;
}

.measure-info strong {
    display: block;

    margin-bottom: 4px;
}

.section {
    border: 1px solid #dfe3e8;

    border-radius: 10px;

    padding: 20px;

    margin-bottom: 20px;
}

.section h2 {
    margin-top: 0;

    font-size: 19px;
}

.radio-option {
    display: flex;

    align-items: flex-start;

    gap: 10px;

    padding: 12px;

    border-radius: 7px;

    cursor: pointer;
}

.radio-option:hover {
    background: #f5f7fa;
}

.radio-option input {
    margin-top: 3px;
}

.option-content {
    flex: 1;
}

.option-title {
    font-weight: bold;

    margin-bottom: 4px;
}

.option-description {
    color: #777;

    font-size: 14px;
}

.time-settings {
    margin-top: 15px;

    padding-top: 15px;

    border-top: 1px solid #e5e7eb;
}

.form-row {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 15px;

    margin-bottom: 15px;
}

.form-group label {
    display: block;

    font-weight: bold;

    margin-bottom: 7px;
}

.form-group input,
.form-group select {
    width: 100%;

    padding: 10px 11px;

    border: 1px solid #cfd4da;

    border-radius: 7px;

    font-size: 16px;
}

.interval {
    max-width: 300px;
}

.checkbox-option {
    display: flex;

    align-items: center;

    gap: 9px;

    margin-top: 15px;

    cursor: pointer;
}

.checkbox-option input {
    width: 17px;

    height: 17px;
}

.help-text {
    color: #777;

    font-size: 14px;

    margin-top: 5px;
}

.form-actions {
    display: flex;

    justify-content: space-between;

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

.button:hover {
    background: #0d65dc;
}

.button-secondary {
    background: #777;
}

.button-secondary:hover {
    background: #666;
}

.hidden {
    display: none;
}

@media (max-width: 700px) {

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

}

</style>

</head>

<body>

<main>

<div class="card">

<h1>Neue Maßnahme</h1>

<div class="subtitle">
Schritt 3 von 3 – Zeitliche Steuerung
</div>


<div class="measure-info">

<strong>
<?= htmlspecialchars(
    (string) ($wizard['name'] ?? '')
) ?>
</strong>

Zeitliche Steuerung der Maßnahme festlegen.

</div>


<form method="post">

<input
    type="hidden"
    name="page"
    value="measure-wizard"
>

<input
    type="hidden"
    name="action"
    value="measure-wizard-step-3"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>


<div class="section">

<h2>Wiedergabe</h2>


<label class="radio-option">

<input
    type="radio"
    name="schedule_type"
    value="once"
    id="schedule-once"
    <?= !$isPeriod ? 'checked' : '' ?>
>

<div class="option-content">

<div class="option-title">
Nur einmal abspielen
</div>

<div class="option-description">
Die Ansage wird einmal abgespielt und danach beendet.
</div>

</div>

</label>


<label class="radio-option">

<input
    type="radio"
    name="schedule_type"
    value="period"
    id="schedule-period"
    <?= $isPeriod ? 'checked' : '' ?>
>

<div class="option-content">

<div class="option-title">
Zeitraum festlegen
</div>

<div class="option-description">
Die Ansage wird innerhalb eines bestimmten Zeitraums wiederholt.
</div>

</div>

</label>


<div
    id="time-settings"
    class="time-settings hidden"
>

<div class="form-row">

<div class="form-group">

<label for="start_at">
Beginn
</label>

<input
    type="datetime-local"
    id="start_at"
    name="start_at"
    value="<?= htmlspecialchars($startAt) ?>"
>

</div>


<div class="form-group">

<label for="end_at">
Ende
</label>

<input
    type="datetime-local"
    id="end_at"
    name="end_at"
    value="<?= htmlspecialchars($endAt) ?>"
>

</div>

</div>


<div class="form-group interval">

<label for="interval">
Intervall der Durchsage
</label>

<select
    id="interval"
    name="interval"
>

<option
    value="5"
    <?= $interval === 5 ? 'selected' : '' ?>
>
5 Minuten
</option>

<option
    value="10"
    <?= $interval === 10 ? 'selected' : '' ?>
>
10 Minuten
</option>

<option
    value="30"
    <?= $interval === 30 ? 'selected' : '' ?>
>
30 Minuten
</option>

<option
    value="60"
    <?= $interval === 60 ? 'selected' : '' ?>
>
1 Stunde
</option>

</select>

</div>


<label class="checkbox-option">

<input
    type="checkbox"
    name="disable_night"
    value="1"
    <?= $disableNight ? 'checked' : '' ?>
>

<span>
Nachts deaktivieren
</span>

</label>

<div class="help-text">
Keine Durchsagen zwischen 22:00 und 06:00 Uhr.
</div>

</div>

</div>


<div class="form-actions">

<a
    href="/?page=measure-wizard&step=2"
    class="button button-secondary"
>
← Zurück
</a>

<button
    type="submit"
    class="button"
>
Weiter zur Zusammenfassung →
</button>

</div>

</form>

</div>

</main>


<script>

(function () {

    const once =
        document.getElementById(
            'schedule-once'
        );

    const period =
        document.getElementById(
            'schedule-period'
        );

    const settings =
        document.getElementById(
            'time-settings'
        );

    const startInput =
        document.getElementById(
            'start_at'
        );

    const endInput =
        document.getElementById(
            'end_at'
        );


    function formatDateTimeLocal(
        date
    ) {

        const year =
            date.getFullYear();

        const month =
            String(
                date.getMonth() + 1
            ).padStart(2, '0');

        const day =
            String(
                date.getDate()
            ).padStart(2, '0');

        const hours =
            String(
                date.getHours()
            ).padStart(2, '0');

        const minutes =
            String(
                date.getMinutes()
            ).padStart(2, '0');

        return (
            year +
            '-' +
            month +
            '-' +
            day +
            'T' +
            hours +
            ':' +
            minutes
        );
    }


    function updateVisibility() {

        if (period.checked) {

            settings.classList.remove(
                'hidden'
            );

        } else {

            settings.classList.add(
                'hidden'
            );
        }
    }


    period.addEventListener(
        'change',
        updateVisibility
    );

    once.addEventListener(
        'change',
        updateVisibility
    );


/*
 * Vorschlagswerte nur bei einer neuen Maßnahme.
 * Vorhandene Werte aus dem PHP-Wizard niemals überschreiben.
 */

if (!startInput.value) {

    const now =
        new Date();

    startInput.value =
        formatDateTimeLocal(
            now
        );
}


if (!endInput.value) {

    const end =
        new Date();

    end.setHours(
        23,
        59,
        0,
        0
    );

    endInput.value =
        formatDateTimeLocal(
            end
        );
}

    updateVisibility();

})();

</script>

</body>

</html>
