<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Maßnahmen – ELA Server</title>

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
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;
    border-radius: 14px;
    padding: 28px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, .08);

    margin-bottom: 24px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;

    margin-bottom: 25px;
}

.topbar h2 {
    margin: 0;
}

.subtitle {
    color: #6b7280;
    margin-top: 5px;
}

.button {
    display: inline-block;

    border: 0;

    padding: 11px 17px;

    border-radius: 9px;

    background: #2563eb;
    color: white;

    text-decoration: none;

    font-weight: 600;

    cursor: pointer;
}

.button:hover {
    background: #1d4ed8;
}

.measure-list {
    display: flex;

    flex-direction: column;

    gap: 12px;
}

.measure-item {
    border: 1px solid #e5e7eb;

    border-radius: 11px;

    padding: 18px;

    background: #f9fafb;

    display: grid;

    grid-template-columns: 1fr auto;

    gap: 20px;

    align-items: center;
}

.measure-name {
    font-size: 18px;

    font-weight: 700;
}

.measure-audio {
    margin-top: 7px;

    font-weight: 600;
}

.measure-meta {
    color: #6b7280;

    font-size: 14px;

    margin-top: 8px;

    line-height: 1.6;
}

.status {
    display: inline-block;

    font-weight: 700;

    margin-bottom: 7px;
}

.status-active {
    color: #15803d;
}

.status-future {
    color: #ca8a04;
}

.status-expired {
    color: #111827;
}

.measure-actions {
    display: flex;

    gap: 10px;

    flex-wrap: wrap;
}

.button-edit {
    background: #2563eb;
}

.button-delete {
    background: #dc2626;
}

.empty {
    padding: 35px;

    text-align: center;

    color: #6b7280;

    border: 2px dashed #d1d5db;

    border-radius: 10px;
}

.back {
    margin-top: 20px;
}

.back a {
    color: #2563eb;

    text-decoration: none;

    font-weight: 600;
}

@media (max-width: 800px) {

    .measure-item {
        grid-template-columns: 1fr;
    }

    .topbar {
        align-items: flex-start;

        flex-direction: column;
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

<h2>Maßnahmen</h2>

<div class="subtitle">
<?= count($measures) ?>
&nbsp;Maßnahme<?= count($measures) === 1 ? '' : 'n' ?>
</div>

</div>

<a
    href="/?page=measure-wizard&step=1&new=1"
    class="button"
>
    + Neue Maßnahme
</a>

</div>


<?php if (!$measures): ?>

<div class="empty">

Noch keine Maßnahmen vorhanden.

</div>

<?php else: ?>

<div class="measure-list">

<?php foreach ($measures as $measure): ?>

<?php

$now = time();

$startTimestamp =
    strtotime(
        (string) $measure['start_at']
    );

$endTimestamp = null;

if (
    !empty($measure['end_at'])
) {
    $endTimestamp =
        strtotime(
            (string) $measure['end_at']
        );
}

if (
    (int) $measure['active'] !== 1
) {

    $statusText = 'Deaktiviert';
    $statusClass = 'status-expired';

} elseif (
    $startTimestamp !== false &&
    $startTimestamp > $now
) {

    $statusText = 'Geplant';
    $statusClass = 'status-future';

} elseif (
    (
        (string) $measure['repeat_type'] === 'once' &&
        $startTimestamp !== false &&
        $startTimestamp + 5 < $now
    ) ||
    (
        (string) $measure['repeat_type'] !== 'once' &&
        $endTimestamp !== null &&
        $endTimestamp !== false &&
        $endTimestamp < $now
    )
) {

    $statusText = 'Abgelaufen';
    $statusClass = 'status-expired';

} else {

    $statusText = 'Aktiv';
    $statusClass = 'status-active';

}

?>

<div class="measure-item">

<div>

<div class="status <?= $statusClass ?>">

<?= htmlspecialchars($statusText) ?>

</div>

<div class="measure-name">

<?= htmlspecialchars(
    (string) $measure['name']
) ?>

</div>

<div class="measure-audio">

🔊
<?= htmlspecialchars(
    (string) $measure['audio_text']
) ?>

</div>

<div class="measure-meta">

Start:
<strong>
<?= htmlspecialchars(
    (string) $measure['start_at']
) ?>
</strong>

<?php if (!empty($measure['end_at'])): ?>

&nbsp; · &nbsp;

Ende:
<strong>
<?= htmlspecialchars(
    (string) $measure['end_at']
) ?>
</strong>

<?php endif; ?>

<br>

Stimme:
<?= htmlspecialchars(
    ucfirst(
        (string) $measure['audio_voice']
    )
) ?>

&nbsp; · &nbsp;

Wiederholung:
<?= htmlspecialchars(
    (string) $measure['repeat_type']
) ?>

<?php if (
    (int) $measure['repeat_interval'] > 1
): ?>

&nbsp; · &nbsp;

Intervall:
<?= (int) $measure['repeat_interval'] ?>

<?php endif; ?>

</div>

</div>

<div class="measure-actions">

<a
    href="/?page=measure-edit&id=<?= (int) $measure['id'] ?>"
    class="button button-edit"
>
    Bearbeiten
</a>

<form
    method="post"
    onsubmit="return confirm('Diese Maßnahme wirklich löschen?');"
>

<input
    type="hidden"
    name="page"
    value="measures"
>

<input
    type="hidden"
    name="action"
    value="measure-delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $measure['id'] ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<button
    type="submit"
    class="button button-delete"
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


<div class="back">

<a href="/">
← Zurück zum Dashboard
</a>

</div>

</main>

</body>

</html>
