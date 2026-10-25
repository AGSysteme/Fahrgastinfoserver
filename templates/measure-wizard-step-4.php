<?php

declare(strict_types=1);

$wizard =
    $_SESSION['measure_wizard'] ?? [];

$targets =
    $wizard['targets'] ?? [];

$schedule =
    $wizard['schedule'] ?? [];

if (!is_array($targets)) {
    $targets = [];
}

if (!is_array($schedule)) {
    $schedule = [];
}

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
    max-width: 1000px;

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

.section {
    border: 1px solid #dfe3e8;

    border-radius: 10px;

    margin-bottom: 20px;

    overflow: hidden;
}

.section-header {
    padding: 14px 18px;

    background: #f3f5f8;

    border-bottom: 1px solid #dfe3e8;

    font-weight: bold;

    font-size: 18px;
}

.section-content {
    padding: 18px;
}

.info-row {
    display: grid;

    grid-template-columns: 180px 1fr;

    gap: 15px;

    padding: 8px 0;

    border-bottom: 1px solid #eee;
}

.info-row:last-child {
    border-bottom: 0;
}

.info-label {
    color: #777;

    font-weight: bold;
}

.line-summary {
    margin-bottom: 18px;
}

.line-summary:last-child {
    margin-bottom: 0;
}

.line-title {
    font-weight: bold;

    font-size: 17px;

    margin-bottom: 8px;
}

.station-summary {
    margin-left: 18px;

    margin-bottom: 12px;
}

.station-title {
    font-weight: bold;

    margin-bottom: 4px;
}

.station-list {
    margin-left: 18px;

    color: #555;

    line-height: 1.6;
}

.badge {
    display: inline-block;

    padding: 4px 9px;

    border-radius: 5px;

    background: #e8f1ff;

    color: #1464c4;

    font-size: 14px;

    font-weight: bold;
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

.button-success {
    background: #16a34a;
}

.button-success:hover {
    background: #12813a;
}

.warning {
    background: #fff8e6;

    border: 1px solid #f0d58a;

    color: #755b16;

    padding: 12px 15px;

    border-radius: 7px;

    margin-bottom: 20px;
}

@media (max-width: 700px) {

    .info-row {
        grid-template-columns: 1fr;
        gap: 3px;
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
Schritt 4 – Zusammenfassung
</div>


<div class="warning">
Bitte überprüfe die Angaben vor dem Erstellen der Maßnahme.
</div>


<!-- ===================================================== -->
<!-- Allgemeine Informationen -->
<!-- ===================================================== -->

<div class="section">

<div class="section-header">
Allgemeine Informationen
</div>

<div class="section-content">

<div class="info-row">

<div class="info-label">
Maßnahme
</div>

<div>
<?= htmlspecialchars(
    (string) ($wizard['name'] ?? '')
) ?>
</div>

</div>


<div class="info-row">

<div class="info-label">
Ansage
</div>

<div>

<?= htmlspecialchars(
    (string) ($audio['text'] ?? '')
) ?>

<?php if (!empty($audio['voice'])): ?>

<span class="badge">
<?= htmlspecialchars(
    ucfirst(
        (string) $audio['voice']
    )
) ?>
</span>

<?php endif; ?>

</div>

</div>

</div>

</div>


<!-- ===================================================== -->
<!-- Haltestellen -->
<!-- ===================================================== -->

<div class="section">

<div class="section-header">
Haltestellen
</div>

<div class="section-content">

<?php if (!$targets): ?>

<div>
Keine Ziele ausgewählt.
</div>

<?php else: ?>

<ul>
<?php foreach ($targets as $stationId): ?>
<?php $station = $stationsRepository->find((int) $stationId); ?>
<li><?= htmlspecialchars((string) ($station['name'] ?? 'Haltestelle nicht mehr vorhanden')) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
</div>
<!-- Zeitliche Steuerung -->
<!-- ===================================================== -->

<div class="section">

<div class="section-header">
Zeitliche Steuerung
</div>

<div class="section-content">

<?php if (
    ($schedule['type'] ?? '') === 'once'
): ?>

<div class="info-row">

<div class="info-label">
Wiedergabe
</div>

<div>
<span class="badge">
Nur einmal abspielen
</span>
</div>

</div>

<?php else: ?>

<div class="info-row">

<div class="info-label">
Wiedergabe
</div>

<div>
<span class="badge">
Zeitraum
</span>
</div>

</div>


<div class="info-row">

<div class="info-label">
Beginn
</div>

<div>
<?= htmlspecialchars(
    (string) ($schedule['start_at'] ?? '')
) ?>
</div>

</div>


<div class="info-row">

<div class="info-label">
Ende
</div>

<div>
<?= htmlspecialchars(
    (string) ($schedule['end_at'] ?? '')
) ?>
</div>

</div>


<div class="info-row">

<div class="info-label">
Intervall
</div>

<div>

<?php

$interval =
    (int) (
        $schedule['interval'] ?? 0
    );

$intervalLabels = [
    5 => '5 Minuten',
    10 => '10 Minuten',
    30 => '30 Minuten',
    60 => '1 Stunde'
];

?>

<?= htmlspecialchars(
    $intervalLabels[$interval]
        ?? 'Unbekannt'
) ?>

</div>

</div>


<div class="info-row">

<div class="info-label">
Nachts deaktivieren
</div>

<div>

<?= !empty(
    $schedule['disable_night']
)
    ? 'Ja – 22:00 bis 06:00 Uhr'
    : 'Nein'
?>

</div>

</div>

<?php endif; ?>

</div>

</div>


<!-- ===================================================== -->
<!-- Aktionen -->
<!-- ===================================================== -->

<div class="form-actions">

<a
    href="/?page=measure-wizard&step=3"
    class="button button-secondary"
>
← Zurück
</a>


<form method="post">

<input
    type="hidden"
    name="page"
    value="measure-wizard"
>

<input
    type="hidden"
    name="action"
    value="measure-wizard-finish"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<button
    type="submit"
    class="button button-success"
>
Maßnahme erstellen
</button>

</form>

</div>

</div>

</main>

</body>

</html>
