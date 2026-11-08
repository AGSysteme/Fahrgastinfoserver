<?php
/**
 * @var array $line
 * @var array $stations        Der Linie zugeordnete Haltestellen (mit sequence)
 * @var array $allStations     Alle Haltestellen
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = 'Haltestellen – Linie ' . $line['number']; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1100px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div>
                    <h2 class="h4 mb-1">
                        Linie <?= htmlspecialchars((string) $line['number']) ?>
                    </h2>

                    <div class="text-muted small">
                        Haltestellen dieser Linie zuordnen und ihre Reihenfolge festlegen.
                    </div>
                </div>

                <a
                    class="btn btn-secondary"
                    href="/?page=lines&action=list"
                >
                    ← Linien
                </a>

            </div>


            <form method="post">

                <input type="hidden" name="page" value="lines">
                <input type="hidden" name="action" value="stations-save">

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

                <div class="row g-4">

                    <!-- =====================================================
                         VERFÜGBARE HALTESTELLEN
                         ===================================================== -->
                    <div class="col-12 col-lg-6">

                        <div class="card h-100">
                            <div class="card-body p-3 p-md-4">

                                <h3 class="h5 mb-1">Verfügbare Haltestellen</h3>

                                <p class="text-muted small mb-3">
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

                                            <div class="handle">☰</div>

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
                                        <div class="drop-zone-empty">
                                            Alle aktiven Haltestellen sind bereits zugeordnet.
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- =====================================================
                         HALTESTELLEN DER LINIE
                         ===================================================== -->
                    <div class="col-12 col-lg-6">

                        <div class="card h-100">
                            <div class="card-body p-3 p-md-4">

                                <h3 class="h5 mb-1">Haltestellen dieser Linie</h3>

                                <p class="text-muted small mb-3">
                                    Hier ziehen und in die gewünschte Reihenfolge bringen.
                                </p>

                                <div
                                    id="assigned-stations"
                                    class="station-list"
                                >

                                    <?php if (!$stations): ?>

                                        <div class="drop-zone-empty">
                                            Noch keine Haltestellen zugeordnet.
                                        </div>

                                    <?php else: ?>

                                        <?php foreach ($stations as $station): ?>

                                            <div
                                                class="station"
                                                draggable="true"
                                                data-station-id="<?= (int) $station['station_id'] ?>"
                                            >

                                                <div class="handle">☰</div>

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

                    </div>

                </div>

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">

                    <div class="text-muted small">
                        ☰ Haltestelle greifen und per Drag &amp; Drop verschieben.
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Haltestellen speichern
                    </button>

                </div>

            </form>

            <div class="mt-4">
                <a href="/?page=lines" class="text-decoration-none">
                    &larr; Zurück zu den Linien
                </a>
            </div>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

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
