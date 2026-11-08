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
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Neue Maßnahme – ELA Server</title>

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
                <h2 class="h4 mb-1">Neue Maßnahme</h2>

                <div class="text-muted small">
                    Schritt 3 von 3 – Zeitliche Steuerung
                </div>
            </div>

            <!-- Hinweis zur Maßnahme -->
            <div class="alert alert-secondary mb-4">
                <div class="fw-semibold">
                    <?= htmlspecialchars((string) ($wizard['name'] ?? '')) ?>
                </div>

                <div class="small">
                    Zeitliche Steuerung der Maßnahme festlegen.
                </div>
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

                <!-- Wiedergabe -->
                <div class="card mb-4">
                    <div class="card-body p-3 p-md-4">

                        <h3 class="h5 mb-3">Wiedergabe</h3>

                        <div class="list-group">

                            <label class="list-group-item d-flex gap-3 align-items-start">
                                <input
                                    type="radio"
                                    name="schedule_type"
                                    value="once"
                                    id="schedule-once"
                                    class="form-check-input mt-1 flex-shrink-0"
                                    <?= !$isPeriod ? 'checked' : '' ?>
                                >

                                <span>
                                    <span class="d-block fw-semibold">Nur einmal abspielen</span>
                                    <span class="d-block text-muted small">
                                        Die Ansage wird einmal abgespielt und danach beendet.
                                    </span>
                                </span>
                            </label>

                            <label class="list-group-item d-flex gap-3 align-items-start">
                                <input
                                    type="radio"
                                    name="schedule_type"
                                    value="period"
                                    id="schedule-period"
                                    class="form-check-input mt-1 flex-shrink-0"
                                    <?= $isPeriod ? 'checked' : '' ?>
                                >

                                <span>
                                    <span class="d-block fw-semibold">Zeitraum festlegen</span>
                                    <span class="d-block text-muted small">
                                        Die Ansage wird innerhalb eines bestimmten Zeitraums wiederholt.
                                    </span>
                                </span>
                            </label>

                        </div>

                        <!-- Zeitraum-Einstellungen -->
                        <div id="time-settings" class="border-top mt-4 pt-4 d-none">

                            <div class="row g-3">

                                <div class="col-12 col-md-6">
                                    <label for="start_at" class="form-label">Beginn</label>

                                    <input
                                        type="datetime-local"
                                        id="start_at"
                                        name="start_at"
                                        class="form-control"
                                        value="<?= htmlspecialchars($startAt) ?>"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="end_at" class="form-label">Ende</label>

                                    <input
                                        type="datetime-local"
                                        id="end_at"
                                        name="end_at"
                                        class="form-control"
                                        value="<?= htmlspecialchars($endAt) ?>"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="interval" class="form-label">
                                        Intervall der Durchsage
                                    </label>

                                    <select
                                        id="interval"
                                        name="interval"
                                        class="form-select"
                                    >
                                        <option value="5" <?= $interval === 5 ? 'selected' : '' ?>>5 Minuten</option>
                                        <option value="10" <?= $interval === 10 ? 'selected' : '' ?>>10 Minuten</option>
                                        <option value="30" <?= $interval === 30 ? 'selected' : '' ?>>30 Minuten</option>
                                        <option value="60" <?= $interval === 60 ? 'selected' : '' ?>>1 Stunde</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input
                                            id="disable_night"
                                            type="checkbox"
                                            name="disable_night"
                                            value="1"
                                            class="form-check-input"
                                            <?= $disableNight ? 'checked' : '' ?>
                                        >

                                        <label for="disable_night" class="form-check-label">
                                            Nachts deaktivieren
                                        </label>
                                    </div>

                                    <div class="form-text">
                                        Keine Durchsagen zwischen 22:00 und 06:00 Uhr.
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

                    <a
                        href="/?page=measure-wizard&step=2"
                        class="btn btn-secondary"
                    >
                        ← Zurück
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Weiter zur Zusammenfassung →
                    </button>

                </div>

            </form>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

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
                'd-none'
            );

        } else {

            settings.classList.add(
                'd-none'
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
