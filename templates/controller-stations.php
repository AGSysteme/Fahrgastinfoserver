<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Controller – Haltestellen
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
    max-width: 1100px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;
    border-radius: 12px;
    padding: 24px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, .08);
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;
    margin-bottom: 30px;
}

.topbar h2 {
    margin: 0 0 8px 0;
}

.info {
    color: #4b5563;
}

.button {
    display: inline-block;

    padding: 10px 16px;

    background: #2563eb;
    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: 600;

    border: 0;

    cursor: pointer;
}

.button:hover {
    background: #1d4ed8;
}

.layout {
    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 25px;
}

.panel {
    border: 1px solid #e5e7eb;
    border-radius: 10px;

    padding: 18px;

    background: #f9fafb;
}

.panel h3 {
    margin-top: 0;
}

.station-list {
    min-height: 100px;
}

.station {
    display: flex;

    align-items: center;

    gap: 12px;

    padding: 13px 15px;

    margin-bottom: 8px;

    background: white;

    border: 1px solid #d1d5db;

    border-radius: 9px;

    cursor: grab;

    user-select: none;

    box-shadow:
        0 1px 2px rgba(0, 0, 0, .04);
}

.station:active {
    cursor: grabbing;
}

.station.dragging {
    opacity: .45;
}

.station.locked {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

.handle {
    color: #6b7280;
    font-size: 20px;
    font-weight: 700;
}

.station-info {
    flex: 1;
}

.station-name {
    font-weight: 700;
}

.station-meta {
    color: #6b7280;
    font-size: 13px;

    margin-top: 2px;
}

.status {
    font-size: 12px;
    color: #6b7280;
}

.empty {
    padding: 25px;

    text-align: center;

    color: #6b7280;

    border: 2px dashed #d1d5db;

    border-radius: 9px;
}

.actions {
    margin-top: 25px;

    display: flex;

    justify-content: space-between;

    align-items: center;
}

.hint {
    color: #6b7280;
    font-size: 14px;
}

@media (max-width: 800px) {

    .layout {
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

<h2>
Haltestellen
</h2>

<div class="info">

Controller:
<strong>
<?= htmlspecialchars(
    (string) $controller['name']
) ?>
</strong>

</div>

</div>

<a
    class="button"
    href="/"
>
← Dashboard
</a>

</div>


<form method="post">

<input
    type="hidden"
    name="page"
    value="controllers"
>

<input
    type="hidden"
    name="action"
    value="stations-save"
>

<input
    type="hidden"
    name="controller_id"
    value="<?= (int) $controller['id'] ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>


<div class="layout">


<!-- =====================================================
     VERFÜGBARE HALTESTELLEN
     ===================================================== -->

<div class="panel">

<h3>
Verfügbare Haltestellen
</h3>

<p class="hint">
Haltestellen greifen und rechts hineinziehen.
</p>

<div
    id="available-stations"
    class="station-list"
>

<?php

$assignedIds = [];

foreach ($stations as $station) {

    if (
        (int) $station['assigned_controller_id']
        === (int) $controller['id']
    ) {
        $assignedIds[] =
            (int) $station['id'];
    }
}

$availableCount = 0;

foreach ($stations as $station):

    $stationId =
        (int) $station['id'];

    $assignedController =
        $station['assigned_controller_id'];

    $assignedToThisController =
        $assignedController !== null
        && (int) $assignedController
            === (int) $controller['id'];

    $assignedToOtherController =
        $assignedController !== null
        && !$assignedToThisController;


    if ($assignedToThisController) {
        continue;
    }

    $availableCount++;

?>

<div
    class="station
        <?= $assignedToOtherController
            ? 'locked'
            : '' ?>"
    <?= $assignedToOtherController
        ? ''
        : 'draggable="true"' ?>
    data-station-id="<?= $stationId ?>"
>

<div class="handle">
☰
</div>

<div class="station-info">

<div class="station-name">

<?= htmlspecialchars(
    (string) $station['name']
) ?>

</div>

<div class="station-meta">

<?php if ($station['short_name'] !== null): ?>

<?= htmlspecialchars(
    (string) $station['short_name']
) ?>

<?php endif; ?>

<?php if ($station['station_code'] !== null): ?>

&nbsp; · &nbsp;

<?= htmlspecialchars(
    (string) $station['station_code']
) ?>

<?php endif; ?>

</div>

</div>

<?php if ($assignedToOtherController): ?>

<div class="status">
bereits vergeben
</div>

<?php endif; ?>

</div>

<?php endforeach; ?>


<?php if ($availableCount === 0): ?>

<div class="empty">
Keine freien Haltestellen vorhanden.
</div>

<?php endif; ?>

</div>

</div>


<!-- =====================================================
     HALTESTELLEN DIESES CONTROLLERS
     ===================================================== -->

<div class="panel">

<h3>
Haltestellen dieses Controllers
</h3>

<p class="hint">
Hier hineinziehen, um die Haltestelle diesem Controller zuzuordnen.
</p>

<div
    id="assigned-stations"
    class="station-list"
>

<?php

$assignedCount = 0;

foreach ($stations as $station):

    if (
        $station['assigned_controller_id'] === null
        || (int) $station['assigned_controller_id']
            !== (int) $controller['id']
    ) {
        continue;
    }

    $assignedCount++;

?>

<div
    class="station"
    draggable="true"
    data-station-id="<?= (int) $station['id'] ?>"
>

<div class="handle">
☰
</div>

<div class="station-info">

<div class="station-name">

<?= htmlspecialchars(
    (string) $station['name']
) ?>

</div>

<div class="station-meta">

<?php if ($station['short_name'] !== null): ?>

<?= htmlspecialchars(
    (string) $station['short_name']
) ?>

<?php endif; ?>

<?php if ($station['station_code'] !== null): ?>

&nbsp; · &nbsp;

<?= htmlspecialchars(
    (string) $station['station_code']
) ?>

<?php endif; ?>

</div>

</div>

</div>

<?php endforeach; ?>


<?php if ($assignedCount === 0): ?>

<div class="empty">
Noch keine Haltestelle zugeordnet.
</div>

<?php endif; ?>

</div>

</div>

</div>


<div class="actions">

<div class="hint">
☰ Haltestelle greifen und per Drag & Drop verschieben.
</div>

<button
    type="submit"
    class="button"
>
Haltestellen speichern
</button>

</div>

</form>


<p style="margin-top: 25px;">

<a href="/">
← Zurück zum Dashboard
</a>

</p>

</div>

</main>


<script>

const available =
    document.getElementById('available-stations');

const assigned =
    document.getElementById('assigned-stations');

let dragged = null;


/*
 * ---------------------------------------------------------
 * Drag starten
 * ---------------------------------------------------------
 */

document.addEventListener('dragstart', function (event) {

    const station =
        event.target.closest('.station');

    if (!station) {
        return;
    }

    if (station.classList.contains('locked')) {
        return;
    }

    dragged = station;

    station.classList.add('dragging');

    event.dataTransfer.effectAllowed = 'move';

    event.dataTransfer.setData(
        'text/plain',
        station.dataset.stationId
    );
});


/*
 * ---------------------------------------------------------
 * Drag beenden
 * ---------------------------------------------------------
 */

document.addEventListener('dragend', function () {

    if (dragged) {
        dragged.classList.remove('dragging');
    }

    dragged = null;
});


/*
 * ---------------------------------------------------------
 * Hilfsfunktion:
 * Station an Position einsetzen
 * ---------------------------------------------------------
 */

function insertStation(list, event) {

    if (!dragged) {
        return;
    }

    const target =
        event.target.closest('.station');

    /*
     * Kein Ziel-Element:
     * einfach ans Ende setzen.
     */
    if (
        !target ||
        target === dragged ||
        !list.contains(target)
    ) {
        list.appendChild(dragged);
        return;
    }

    const rect =
        target.getBoundingClientRect();

    const middle =
        rect.top +
        rect.height / 2;

    if (event.clientY < middle) {

        list.insertBefore(
            dragged,
            target
        );

    } else {

        list.insertBefore(
            dragged,
            target.nextSibling
        );
    }
}


/*
 * ---------------------------------------------------------
 * Beide Bereiche akzeptieren Drag & Drop
 * ---------------------------------------------------------
 */

[available, assigned].forEach(function (list) {

    list.addEventListener(
        'dragover',
        function (event) {

            event.preventDefault();

            if (!dragged) {
                return;
            }

            event.dataTransfer.dropEffect =
                'move';
        }
    );


    list.addEventListener(
        'drop',
        function (event) {

            event.preventDefault();
            event.stopPropagation();

            if (!dragged) {
                return;
            }

            insertStation(
                list,
                event
            );
        }
    );

});


/*
 * ---------------------------------------------------------
 * Beim Speichern:
 * stations[] aus dem rechten Bereich erzeugen
 * ---------------------------------------------------------
 */

document.querySelector('form').addEventListener(
    'submit',
    function () {

        /*
         * Bereits erzeugte Hidden-Felder entfernen.
         */
        this.querySelectorAll(
            'input[data-station-field="1"]'
        ).forEach(function (input) {

            input.remove();

        });


        /*
         * Nur die Stationen rechts gehören
         * zu diesem Controller.
         */
        assigned
            .querySelectorAll('.station')
            .forEach(function (station) {

                const input =
                    document.createElement('input');

                input.type = 'hidden';

                input.name =
                    'stations[]';

                input.value =
                    station.dataset.stationId;

                input.dataset.stationField =
                    '1';

                this.appendChild(input);

            }.bind(this));

    }
);

</script>





</body>

</html>

