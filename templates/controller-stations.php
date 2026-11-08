<?php
/**
 * @var array $controller
 * @var array $stations
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = 'Haltestellen – Controller ' . $controller['name']; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1100px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div>
                    <h2 class="h4 mb-1">Haltestellen</h2>

                    <div class="text-muted small">
                        Controller:
                        <strong>
                            <?= htmlspecialchars((string) $controller['name']) ?>
                        </strong>
                    </div>
                </div>

                <a
                    class="btn btn-secondary"
                    href="/"
                >
                    ← Dashboard
                </a>

            </div>


            <form method="post">

                <input type="hidden" name="page" value="controllers">
                <input type="hidden" name="action" value="stations-save">

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

                <div class="row g-4">

                    <!-- =====================================================
                         VERFÜGBARE HALTESTELLEN
                         ===================================================== -->
                    <div class="col-12 col-lg-6">

                        <div class="card h-100">
                            <div class="card-body p-3 p-md-4">

                                <h3 class="h5 mb-1">Verfügbare Haltestellen</h3>

                                <p class="text-muted small mb-3">
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
                                            class="station <?= $assignedToOtherController ? 'locked' : '' ?>"
                                            <?= $assignedToOtherController
                                                ? ''
                                                : 'draggable="true"' ?>
                                            data-station-id="<?= $stationId ?>"
                                        >

                                            <div class="handle">☰</div>

                                            <div class="station-info">

                                                <div class="station-name">
                                                    <?= htmlspecialchars(
                                                        (string) $station['name']
                                                    ) ?>
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
                                        <div class="drop-zone-empty">
                                            Keine freien Haltestellen vorhanden.
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- =====================================================
                         HALTESTELLEN DIESES CONTROLLERS
                         ===================================================== -->
                    <div class="col-12 col-lg-6">

                        <div class="card h-100">
                            <div class="card-body p-3 p-md-4">

                                <h3 class="h5 mb-1">Haltestellen dieses Controllers</h3>

                                <p class="text-muted small mb-3">
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
                                            data-pin="<?= $station['output_pin'] !== null ? (int) $station['output_pin'] : '' ?>"
                                        >

                                            <div class="handle">☰</div>

                                            <div class="station-info">

                                                <div class="station-name">
                                                    <?= htmlspecialchars(
                                                        (string) $station['name']
                                                    ) ?>
                                                </div>


                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                    <?php if ($assignedCount === 0): ?>
                                        <div class="drop-zone-empty">
                                            Noch keine Haltestelle zugeordnet.
                                        </div>
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
                <a href="/" class="text-decoration-none">
                    &larr; Zurück zum Dashboard
                </a>
            </div>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

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

    // Auswahlfeld des Ausgangs darf kein Ziehen auslösen
    if (event.target.closest('select')) {
        event.preventDefault();
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
    syncPins();
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

            syncPins();
        }
    );

});


/*
 * ---------------------------------------------------------
 * Ausgangs-Auswahl: nur bei zugeordneten Haltestellen
 * ---------------------------------------------------------
 */

function makePinSelect(stationId, pin) {
    const select = document.createElement('select');
    select.name = 'pins[' + stationId + ']';
    select.className = 'form-select form-select-sm pin-select';
    select.setAttribute('aria-label', 'Ausgang');

    const empty = document.createElement('option');
    empty.value = '';
    empty.textContent = '–';
    select.appendChild(empty);

    for (let n = 1; n <= 16; n++) {
        const option = document.createElement('option');
        option.value = String(n);
        option.textContent = String(n);
        if (String(n) === String(pin)) {
            option.selected = true;
        }
        select.appendChild(option);
    }

    return select;
}

function syncPins() {
    available
        .querySelectorAll('.pin-select')
        .forEach(select => select.remove());

    assigned
        .querySelectorAll('.station')
        .forEach(station => {
            if (!station.querySelector('.pin-select')) {
                station.appendChild(
                    makePinSelect(station.dataset.stationId, station.dataset.pin || '')
                );
            }
        });
}

syncPins();


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
