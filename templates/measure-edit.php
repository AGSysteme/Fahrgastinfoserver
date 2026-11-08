<?php
/**
 * @var array $measure
 * @var array $wizardLines
 * @var string $csrfToken
 */

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
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Maßnahme bearbeiten – ELA Server</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>

<body>

<header class="py-4 px-4 px-md-5">
    <h1 class="h3 mb-0">ELA Server</h1>
</header>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 900px;">

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">Maßnahme bearbeiten</h2>

                <div class="text-muted small">
                    Maßnahme #<?= $measureId ?>
                </div>
            </div>

            <!-- Ansage -->
            <div class="alert alert-secondary mb-4">
                <div class="fw-semibold mb-1">Ansage:</div>

                <div>
                    <?= htmlspecialchars($audioText) ?>
                </div>

                <?php if ($audioVoice !== ''): ?>
                    <div class="small text-muted mt-1">
                        Stimme:
                        <?= htmlspecialchars(ucfirst($audioVoice)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="post">

                <input type="hidden" name="page" value="measure-edit">
                <input type="hidden" name="action" value="measure-edit-update">

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

                <!-- Grunddaten -->
                <div class="row g-3">

                    <div class="col-12">
                        <label for="name" class="form-label">
                            Name der Maßnahme <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                (string) ($measure['name'] ?? '')
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="start_at" class="form-label">
                            Beginn <span class="text-danger">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="start_at"
                            name="start_at"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                date(
                                    'Y-m-d\TH:i',
                                    strtotime($startAt)
                                )
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="end_at" class="form-label">
                            Ende
                        </label>

                        <input
                            type="datetime-local"
                            id="end_at"
                            name="end_at"
                            class="form-control"
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

                    <div class="col-12 col-md-6">
                        <label for="repeat_type" class="form-label">
                            Wiederholung
                        </label>

                        <select
                            id="repeat_type"
                            name="repeat_type"
                            class="form-select"
                        >
                            <option
                                value="once"
                                <?= $repeatType === 'once' ? 'selected' : '' ?>
                            >
                                Nur einmal
                            </option>

                            <option
                                value="interval"
                                <?= $repeatType === 'interval' ? 'selected' : '' ?>
                            >
                                Intervall
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="repeat_interval" class="form-label">
                            Intervall
                        </label>

                        <select
                            id="repeat_interval"
                            name="repeat_interval"
                            class="form-select"
                        >
                            <option value="5" <?= $repeatInterval === 5 ? 'selected' : '' ?>>5 Minuten</option>
                            <option value="10" <?= $repeatInterval === 10 ? 'selected' : '' ?>>10 Minuten</option>
                            <option value="30" <?= $repeatInterval === 30 ? 'selected' : '' ?>>30 Minuten</option>
                            <option value="60" <?= $repeatInterval === 60 ? 'selected' : '' ?>>1 Stunde</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input
                                type="checkbox"
                                id="active"
                                name="active"
                                value="1"
                                class="form-check-input"
                                role="switch"
                                <?= $active ? 'checked' : '' ?>
                            >

                            <label for="active" class="form-check-label">
                                Maßnahme aktiv
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input
                                type="checkbox"
                                id="disable_night"
                                name="disable_night"
                                value="1"
                                class="form-check-input"
                                role="switch"
                                <?= $disableNight ? 'checked' : '' ?>
                            >

                            <label for="disable_night" class="form-check-label">
                                Nachts deaktivieren (22:00–06:00)
                            </label>
                        </div>
                    </div>

                </div>

                <hr class="my-4">

                <!-- Linien, Richtungen und Haltestellen -->
                <h3 class="h5 mb-1">Linien, Richtungen und Haltestellen</h3>

                <p class="text-muted small mb-3">
                    Wählen Sie aus, wo diese Maßnahme abgespielt werden soll.
                </p>

                <div class="mb-3">
                    <label for="line-filter" class="form-label">
                        Linien filtern
                    </label>

                    <input
                        type="search"
                        id="line-filter"
                        class="form-control"
                        placeholder="z. B. 100 oder Hauptbahnhof"
                        autocomplete="off"
                    >
                </div>

                <div id="line-list" class="d-flex flex-column gap-3">

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
                            class="line card"
                            data-line-search="<?= htmlspecialchars(
                                $lineSearch
                            ) ?>"
                        >

                            <div class="line-header card-header d-flex align-items-center gap-2">

                                <input
                                    type="checkbox"
                                    class="line-checkbox form-check-input mt-0"
                                    id="line-<?= $lineId ?>"
                                    data-line-id="<?= $lineId ?>"
                                    <?= $lineHasSelection ? 'checked' : '' ?>
                                >

                                <label for="line-<?= $lineId ?>" class="form-check-label fw-semibold mb-0">

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
                                class="line-content card-body"
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
                                        class="direction border rounded-3 p-3 mb-3"
                                        data-direction-search="<?= htmlspecialchars(
                                            $directionSearch
                                        ) ?>"
                                    >

                                        <div class="direction-header d-flex align-items-center gap-2">

                                            <input
                                                type="checkbox"
                                                class="direction-checkbox form-check-input mt-0"
                                                id="direction-<?= $directionId ?>"
                                                name="directions[]"
                                                value="<?= $directionId ?>"
                                                <?= $selectedCount > 0 ? 'checked' : '' ?>
                                            >

                                            <label for="direction-<?= $directionId ?>" class="form-check-label fw-semibold mb-0">

                                                <?= htmlspecialchars(
                                                    (string) $direction['name']
                                                ) ?>

                                                <?php if (!empty($direction['destination'])): ?>
                                                    <span class="direction-meta text-muted fw-normal">
                                                        → <?= htmlspecialchars(
                                                            (string) $direction['destination']
                                                        ) ?>
                                                    </span>
                                                <?php endif; ?>

                                                <span
                                                    class="direction-meta text-muted fw-normal"
                                                    id="count-<?= $directionId ?>"
                                                >
                                                    (<?= $selectedCount ?>/<?= $totalCount ?>)
                                                </span>

                                            </label>

                                        </div>

                                        <div
                                            class="station-selector mt-3"
                                            <?= $selectedCount > 0 ? '' : 'hidden' ?>
                                        >

                                            <div class="selector-actions d-flex gap-2 mb-3">

                                                <button
                                                    type="button"
                                                    class="small-button select-all btn btn-sm btn-outline-primary"
                                                    data-direction="<?= $directionId ?>"
                                                >
                                                    Alle →
                                                </button>

                                                <button
                                                    type="button"
                                                    class="small-button deselect-all btn btn-sm btn-outline-secondary"
                                                    data-direction="<?= $directionId ?>"
                                                >
                                                    ← Alle
                                                </button>

                                            </div>

                                            <div class="station-columns row g-3">

                                                <div class="station-column col-12 col-md-6">

                                                    <div class="station-column-title fw-semibold small text-muted mb-2">
                                                        Nicht ausgewählt
                                                    </div>

                                                    <div
                                                        class="station-list station-unselected list-group"
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

                                                            <label class="station-item list-group-item d-flex align-items-center gap-2">

                                                                <input
                                                                    type="checkbox"
                                                                    class="station-checkbox form-check-input mt-0"
                                                                    name="stations[<?= $directionId ?>][]"
                                                                    value="<?= $stationId ?>"
                                                                    data-direction="<?= $directionId ?>"
                                                                >

                                                                <span>
                                                                    <?= htmlspecialchars(
                                                                        (string) $station['name']
                                                                    ) ?>

                                                                    <?php if (!empty($station['short_name'])): ?>
                                                                        <span class="text-muted">(<?= htmlspecialchars(
                                                                            (string) $station['short_name']
                                                                        ) ?>)</span>
                                                                    <?php endif; ?>
                                                                </span>

                                                            </label>

                                                        <?php endforeach; ?>

                                                        <?php if (!$hasUnselected): ?>
                                                            <div class="station-empty list-group-item text-muted small">
                                                                Keine weiteren Haltestellen
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>

                                                </div>

                                                <div class="station-column col-12 col-md-6">

                                                    <div class="station-column-title fw-semibold small text-muted mb-2">
                                                        Ausgewählt
                                                    </div>

                                                    <div
                                                        class="station-list station-selected list-group"
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

                                                            <label class="station-item list-group-item d-flex align-items-center gap-2">

                                                                <input
                                                                    type="checkbox"
                                                                    class="station-checkbox form-check-input mt-0"
                                                                    name="stations[<?= $directionId ?>][]"
                                                                    value="<?= $stationId ?>"
                                                                    data-direction="<?= $directionId ?>"
                                                                    checked
                                                                >

                                                                <span>
                                                                    <?= htmlspecialchars(
                                                                        (string) $station['name']
                                                                    ) ?>

                                                                    <?php if (!empty($station['short_name'])): ?>
                                                                        <span class="text-muted">(<?= htmlspecialchars(
                                                                            (string) $station['short_name']
                                                                        ) ?>)</span>
                                                                    <?php endif; ?>
                                                                </span>

                                                            </label>

                                                        <?php endforeach; ?>

                                                        <?php if (!$hasSelected): ?>
                                                            <div class="station-empty list-group-item text-muted small">
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

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

                    <a
                        href="/?page=measures"
                        class="btn btn-secondary"
                    >
                        ← Abbrechen
                    </a>

                    <button type="submit" class="btn btn-success">
                        Änderungen speichern
                    </button>

                </div>

            </form>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
