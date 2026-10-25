<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Haltestellen – Linie
<?= htmlspecialchars((string) $line['number']) ?>
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

.sequence {
    min-width: 32px;

    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;

    background: #2563eb;

    color: white;

    border-radius: 50%;

    font-size: 14px;

    font-weight: 700;
}

.drop-placeholder {
    height: 55px;

    margin-bottom: 8px;

    border: 2px dashed #60a5fa;

    border-radius: 9px;

    background: #eff6ff;
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
Linie <?= htmlspecialchars((string) $line['number']) ?>
</h2>

<div class="info">
Haltestellen dieser Linie zuordnen und ihre Reihenfolge festlegen.
</div>
</div>

<a
    class="button"
    href="/?page=lines&action=list"
>
← Linien
</a>

</div>


<?php if ($error !== null): ?>
<p role="alert" style="color: #991b1b; background: #fee2e2; padding: 12px;">
<?= htmlspecialchars($error) ?>
</p>
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
    value="stations-save"
>

<input
    type="hidden"
    name="line_id"
    value="<?= (int) $line['id'] ?>"
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
Haltestellen hier greifen und rechts hineinziehen.
</p>

<div
    id="available-stations"
    class="station-list"
>

<?php

$assignedIds = [];

foreach ($stations as $assignedStation) {
    $assignedIds[] =
        (int) $assignedStation['station_id'];
}

?>

<?php

$availableCount = 0;

foreach ($allStations as $station):

    if (
        in_array(
            (int) $station['id'],
            $assignedIds,
            true
        )
    ) {
        continue;
    }

    $availableCount++;

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


<?php if ($availableCount === 0): ?>

<div class="empty">
Alle aktiven Haltestellen sind bereits zugeordnet.
</div>

<?php endif; ?>

</div>

</div>


<!-- =====================================================
     HALTESTELLEN DER RICHTUNG
     ===================================================== -->

<div class="panel">

<h3>
Haltestellen dieser Linie
</h3>

<p class="hint">
Hier ziehen und in die gewünschte Reihenfolge bringen.
</p>

<div
    id="assigned-stations"
    class="station-list"
>

<?php if (!$stations): ?>

<div class="empty">
Noch keine Haltestellen zugeordnet.
</div>

<?php else: ?>

<?php foreach ($stations as $station): ?>

<div
    class="station"
    draggable="true"
    data-station-id="<?= (int) $station['station_id'] ?>"
>

<div class="handle">
☰
</div>

<div class="sequence">
<?= (int) $station['sequence'] ?>
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

<a href="/?page=lines">
← Zurück zu den Linien
</a>

</p>

</div>

</main>


<script>

const available =
    document.getElementById(
        'available-stations'
    );

const assigned =
    document.getElementById(
        'assigned-stations'
    );

let dragged = null;


/*
 * ---------------------------------------------------------
 * Drag starten
 * ---------------------------------------------------------
 */

document.addEventListener(
    'dragstart',
    function (event) {

        const station =
            event.target.closest('.station');

        if (!station) {
            return;
        }

        dragged = station;

        station.classList.add('dragging');

        event.dataTransfer.effectAllowed =
            'move';

        event.dataTransfer.setData(
            'text/plain',
            station.dataset.stationId
        );
    }
);


/*
 * ---------------------------------------------------------
 * Drag beenden
 * ---------------------------------------------------------
 */

document.addEventListener(
    'dragend',
    function () {

        if (dragged) {
            dragged.classList.remove(
                'dragging'
            );
        }

        dragged = null;

        updateSequences();
    }
);


/*
 * ---------------------------------------------------------
 * Drag Over
 * ---------------------------------------------------------
 */

[available, assigned].forEach(
    function (list) {

        list.addEventListener(
            'dragover',
            function (event) {

                event.preventDefault();

                if (!dragged) {
                    return;
                }

                const target =
                    event.target.closest(
                        '.station'
                    );

                if (
                    !target ||
                    target === dragged
                ) {
                    return;
                }

                /*
                 * Wenn Maus oberhalb der Mitte
                 * der Zielstation ist:
                 * davor einfügen.
                 */

                const rect =
                    target.getBoundingClientRect();

                const middle =
                    rect.top +
                    rect.height / 2;

                if (event.clientY < middle) {

                    target.parentNode.insertBefore(
                        dragged,
                        target
                    );

                } else {

                    target.parentNode.insertBefore(
                        dragged,
                        target.nextSibling
                    );
                }

            }
        );

        list.addEventListener(
            'drop',
            function (event) {

                event.preventDefault();

                if (!dragged) {
                    return;
                }

                /*
                 * Falls in eine leere Liste
                 * gezogen wurde.
                 */

                if (
                    event.target === list ||
                    !event.target.closest('.station')
                ) {

                    list.appendChild(
                        dragged
                    );
                }

                updateSequences();
            }
        );

    }
);


/*
 * ---------------------------------------------------------
 * Nummerierung aktualisieren
 * ---------------------------------------------------------
 */

function updateSequences()
{
    const stations =
        assigned.querySelectorAll(
            '.station'
        );

    /*
     * Alte Hidden-Felder entfernen
     */
    document
        .querySelectorAll(
            'input[name="station_ids[]"]'
        )
        .forEach(
            function (input) {
                input.remove();
            }
        );

    /*
     * Neue Reihenfolge erzeugen
     */
    stations.forEach(
        function (station, index) {

            let sequence =
                station.querySelector(
                    '.sequence'
                );

            if (!sequence) {

                sequence =
                    document.createElement(
                        'div'
                    );

                sequence.className =
                    'sequence';

                station.insertBefore(
                    sequence,
                    station.querySelector(
                        '.station-info'
                    )
                );
            }

            sequence.textContent =
                index + 1;

            const input =
                document.createElement(
                    'input'
                );

            input.type = 'hidden';

            input.name =
                'station_ids[]';

            input.value =
                station.dataset.stationId;

            document
                .querySelector('form')
                .appendChild(input);
        }
    );
}

updateSequences();

</script>

</body>

</html>
