<?php

declare(strict_types=1);

$measureId =
    (int) ($measure['id'] ?? 0);

$audioText =
    (string) ($measure['audio_text'] ?? '');

$audioVoice =
    (string) ($measure['audio_voice'] ?? '');

$startAt =
    (string) ($measure['start_at'] ?? '');

$endAt =
    (string) ($measure['end_at'] ?? '');

$repeatType =
    (string) ($measure['repeat_type'] ?? 'once');

$repeatInterval =
    (int) ($measure['repeat_interval'] ?? 1);

$active =
    !empty($measure['active']);

$disableNight =
    !empty($measure['disable_night']);

?>

<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Maßnahme bearbeiten – ELA Server</title>

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

.info {
    background: #f3f5f8;

    border: 1px solid #dfe3e8;

    border-radius: 8px;

    padding: 15px;

    margin-bottom: 25px;
}

label {
    display: block;

    font-weight: bold;

    margin-bottom: 7px;
}

input,
select {
    width: 100%;

    padding: 10px;

    border: 1px solid #ccc;

    border-radius: 6px;

    font-size: 16px;
}

.form-row {
    margin-bottom: 20px;
}

.checkbox {
    display: flex;

    align-items: center;

    gap: 10px;
}

.checkbox input {
    width: auto;
}

.actions {
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

@media (max-width: 700px) {

    .actions {
        flex-direction: column-reverse;
    }

}

</style>

</head>

<body>

<main>

<div class="card">

<h1>Maßnahme bearbeiten</h1>

<div class="subtitle">
Maßnahme #<?= $measureId ?>
</div>


<div class="info">

<strong>Ansage:</strong><br>

<?= htmlspecialchars($audioText) ?>

<?php if ($audioVoice !== ''): ?>

<br>

Stimme:
<?= htmlspecialchars(ucfirst($audioVoice)) ?>

<?php endif; ?>

</div>


<form method="post">

<input
    type="hidden"
    name="page"
    value="measure-edit"
>

<input
    type="hidden"
    name="action"
    value="measure-edit-update"
>

<input
    type="hidden"
    name="id"
    value="<?= $measureId ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>


<div class="form-row">

<label for="name">
Name der Maßnahme
</label>

<input
    type="text"
    id="name"
    name="name"
    value="<?= htmlspecialchars(
        (string) ($measure['name'] ?? '')
    ) ?>"
    required
>

</div>


<div class="form-row">

<label for="start_at">
Beginn
</label>

<input
    type="datetime-local"
    id="start_at"
    name="start_at"
    value="<?= htmlspecialchars(
        date(
            'Y-m-d\TH:i',
            strtotime($startAt)
        )
    ) ?>"
    required
>

</div>


<div class="form-row">

<label for="end_at">
Ende
</label>

<input
    type="datetime-local"
    id="end_at"
    name="end_at"
    value="<?= $endAt !== ''
        ? htmlspecialchars(
            date(
                'Y-m-d\TH:i',
                strtotime($endAt)
            )
        )
        : ''
    ?>"
>

</div>


<div class="form-row">

<label for="repeat_type">
Wiederholung
</label>

<select
    id="repeat_type"
    name="repeat_type"
>

<option
    value="once"
    <?= $repeatType === 'once'
        ? 'selected'
        : ''
    ?>
>
Nur einmal
</option>

<option
    value="interval"
    <?= $repeatType === 'interval'
        ? 'selected'
        : ''
    ?>
>
Intervall
</option>

</select>

</div>


<div class="form-row">

<label for="repeat_interval">
Intervall
</label>

<select
    id="repeat_interval"
    name="repeat_interval"
>

<option
    value="5"
    <?= $repeatInterval === 5
        ? 'selected'
        : ''
    ?>
>
5 Minuten
</option>

<option
    value="10"
    <?= $repeatInterval === 10
        ? 'selected'
        : ''
    ?>
>
10 Minuten
</option>

<option
    value="30"
    <?= $repeatInterval === 30
        ? 'selected'
        : ''
    ?>
>
30 Minuten
</option>

<option
    value="60"
    <?= $repeatInterval === 60
        ? 'selected'
        : ''
    ?>
>
1 Stunde
</option>

</select>

</div>


<div class="form-row checkbox">

<input
    type="checkbox"
    id="active"
    name="active"
    value="1"
    <?= $active ? 'checked' : '' ?>
>

<label for="active">
Maßnahme aktiv
</label>

</div>


<div class="form-row checkbox">

<input
    type="checkbox"
    id="disable_night"
    name="disable_night"
    value="1"
    <?= $disableNight ? 'checked' : '' ?>
>

<label for="disable_night">
Nachts deaktivieren (22:00–06:00)
</label>

</div>


<hr style="
    margin: 30px 0;
    border: 0;
    border-top: 1px solid #ddd;
">


<h2>Linien, Richtungen und Haltestellen</h2>

<p class="subtitle">
Wählen Sie aus, wo diese Maßnahme abgespielt werden soll.
</p>


<div class="filter">

<label for="line-filter">
Linien filtern
</label>

<input
    type="search"
    id="line-filter"
    placeholder="z. B. 100 oder Hauptbahnhof"
    autocomplete="off"
>

</div>


<div id="line-list">

<?php foreach ($wizardLines as $line): ?>

<?php

$lineId =
    (int) $line['id'];

$lineSearch =
    strtolower(
        (string) $line['number']
        . ' '
        . (string) ($line['name'] ?? '')
    );

$lineHasSelection = false;

foreach ($line['directions'] as $lineDirection) {

    if (
        !empty(
            $lineDirection['selected_station_ids']
        )
    ) {
        $lineHasSelection = true;
        break;
    }
}

?>

<div
    class="line"
    data-line-search="<?= htmlspecialchars(
        $lineSearch
    ) ?>"
>

<div class="line-header">

<input
    type="checkbox"
    class="line-checkbox"
    id="line-<?= $lineId ?>"
    data-line-id="<?= $lineId ?>"
    <?= $lineHasSelection ? 'checked' : '' ?>
>

<label for="line-<?= $lineId ?>">

Linie
<?= htmlspecialchars(
    (string) $line['number']
) ?>

<?php if (!empty($line['name'])): ?>

– <?= htmlspecialchars(
    (string) $line['name']
) ?>

<?php endif; ?>

</label>

</div>


<div
    class="line-content"
    <?= $lineHasSelection ? '' : 'hidden' ?>
>


<?php foreach ($line['directions'] as $direction): ?>

<?php

$directionId =
    (int) $direction['id'];

$selectedStationIds =
    $direction['selected_station_ids']
    ?? [];

$selectedCount =
    count($selectedStationIds);

$totalCount =
    count($direction['stations']);

$directionSearch =
    strtolower(
        (string) $direction['name']
        . ' '
        . (string) ($direction['destination'] ?? '')
    );

?>

<div
    class="direction"
    data-direction-search="<?= htmlspecialchars(
        $directionSearch
    ) ?>"
>

<div class="direction-header">

<input
    type="checkbox"
    class="direction-checkbox"
    id="direction-<?= $directionId ?>"
    name="directions[]"
    value="<?= $directionId ?>"
    <?= $selectedCount > 0 ? 'checked' : '' ?>
>

<label for="direction-<?= $directionId ?>">

<?= htmlspecialchars(
    (string) $direction['name']
) ?>

<?php if (!empty($direction['destination'])): ?>

<span class="direction-meta">
→ <?= htmlspecialchars(
    (string) $direction['destination']
) ?>
</span>

<?php endif; ?>

<span
    class="direction-meta"
    id="count-<?= $directionId ?>"
>
(
<?= $selectedCount ?>
/
<?= $totalCount ?>
)
</span>

</label>

</div>


<div
    class="station-selector"
    <?= $selectedCount > 0 ? '' : 'hidden' ?>
>

<div class="selector-actions">

<button
    type="button"
    class="small-button select-all"
    data-direction="<?= $directionId ?>"
>
Alle →
</button>

<button
    type="button"
    class="small-button deselect-all"
    data-direction="<?= $directionId ?>"
>
← Alle
</button>

</div>


<div class="station-columns">

<div class="station-column">

<div class="station-column-title">
Nicht ausgewählt
</div>

<div
    class="station-list station-unselected"
    data-direction="<?= $directionId ?>"
>

<?php

$hasUnselected = false;

foreach ($direction['stations'] as $station):

    $stationId =
        (int) $station['station_id'];

    $isSelected =
        in_array(
            $stationId,
            $selectedStationIds,
            true
        );

    if ($isSelected) {
        continue;
    }

    $hasUnselected = true;

?>

<label class="station-item">

<input
    type="checkbox"
    class="station-checkbox"
    name="stations[<?= $directionId ?>][]"
    value="<?= $stationId ?>"
    data-direction="<?= $directionId ?>"
>

<?= htmlspecialchars(
    (string) $station['name']
) ?>

<?php if (!empty($station['short_name'])): ?>

(<?= htmlspecialchars(
    (string) $station['short_name']
) ?>)

<?php endif; ?>

</label>

<?php endforeach; ?>

<?php if (!$hasUnselected): ?>

<div class="station-empty">
Keine weiteren Haltestellen
</div>

<?php endif; ?>

</div>

</div>


<div class="station-column">

<div class="station-column-title">
Ausgewählt
</div>

<div
    class="station-list station-selected"
    data-direction="<?= $directionId ?>"
>

<?php

$hasSelected = false;

foreach ($direction['stations'] as $station):

    $stationId =
        (int) $station['station_id'];

    $isSelected =
        in_array(
            $stationId,
            $selectedStationIds,
            true
        );

    if (!$isSelected) {
        continue;
    }

    $hasSelected = true;

?>

<label class="station-item">

<input
    type="checkbox"
    class="station-checkbox"
    name="stations[<?= $directionId ?>][]"
    value="<?= $stationId ?>"
    data-direction="<?= $directionId ?>"
    checked
>

<?= htmlspecialchars(
    (string) $station['name']
) ?>

<?php if (!empty($station['short_name'])): ?>

(<?= htmlspecialchars(
    (string) $station['short_name']
) ?>)

<?php endif; ?>

</label>

<?php endforeach; ?>

<?php if (!$hasSelected): ?>

<div class="station-empty">
Keine Haltestellen ausgewählt
</div>

<?php endif; ?>

</div>

</div>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<?php endforeach; ?>

</div>


<div class="actions">

<a
    href="/?page=measures"
    class="button button-secondary"
>
← Abbrechen
</a>

<button
    type="submit"
    class="button button-success"
>
Änderungen speichern
</button>

</div>

</form>

</div>

</main>

</body>

</html>
