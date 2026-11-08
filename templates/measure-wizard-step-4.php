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
<?php $pageTitle = 'Neue Maßnahme'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1000px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">Neue Maßnahme</h2>

                <div class="text-muted small">
                    Schritt 4 – Zusammenfassung
                </div>
            </div>

            <!-- Hinweis -->
            <div class="alert alert-warning mb-4" role="alert">
                Bitte überprüfe die Angaben vor dem Erstellen der Maßnahme.
            </div>

            <!-- Allgemeine Informationen -->
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    Allgemeine Informationen
                </div>

                <div class="card-body p-0">

                    <div class="row g-0 border-bottom px-3 py-2">
                        <div class="col-12 col-md-4 text-muted fw-semibold">
                            Maßnahme
                        </div>
                        <div class="col-12 col-md-8">
                            <?= htmlspecialchars(
                                (string) ($wizard['name'] ?? '')
                            ) ?>
                        </div>
                    </div>

                    <div class="row g-0 px-3 py-2">
                        <div class="col-12 col-md-4 text-muted fw-semibold">
                            Ansage
                        </div>
                        <div class="col-12 col-md-8">
                            <?= htmlspecialchars(
                                (string) ($audio['text'] ?? '')
                            ) ?>

                            <?php if (!empty($audio['voice'])): ?>
                                <span class="badge text-bg-primary ms-1">
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

            <!-- Haltestellen -->
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    Haltestellen
                </div>

                <div class="card-body">

                    <?php if (!$targets): ?>

                        <div class="text-muted">
                            Keine Ziele ausgewählt.
                        </div>

                    <?php else: ?>

                        <ul class="mb-0">
                            <?php foreach ($targets as $stationId): ?>
                                <?php $station = $stationsRepository->find((int) $stationId); ?>
                                <li><?= htmlspecialchars((string) ($station['name'] ?? 'Haltestelle nicht mehr vorhanden')) ?></li>
                            <?php endforeach; ?>
                        </ul>

                    <?php endif; ?>

                </div>
            </div>

            <!-- Zeitliche Steuerung -->
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    Zeitliche Steuerung
                </div>

                <div class="card-body p-0">

                    <?php if (
                        ($schedule['type'] ?? '') === 'once'
                    ): ?>

                        <div class="row g-0 px-3 py-2">
                            <div class="col-12 col-md-4 text-muted fw-semibold">
                                Wiedergabe
                            </div>
                            <div class="col-12 col-md-8">
                                <span class="badge text-bg-primary">
                                    Nur einmal abspielen
                                </span>
                            </div>
                        </div>

                    <?php else: ?>

                        <div class="row g-0 border-bottom px-3 py-2">
                            <div class="col-12 col-md-4 text-muted fw-semibold">
                                Wiedergabe
                            </div>
                            <div class="col-12 col-md-8">
                                <span class="badge text-bg-primary">
                                    Zeitraum
                                </span>
                            </div>
                        </div>

                        <div class="row g-0 border-bottom px-3 py-2">
                            <div class="col-12 col-md-4 text-muted fw-semibold">
                                Beginn
                            </div>
                            <div class="col-12 col-md-8">
                                <?= htmlspecialchars(
                                    (string) ($schedule['start_at'] ?? '')
                                ) ?>
                            </div>
                        </div>

                        <div class="row g-0 border-bottom px-3 py-2">
                            <div class="col-12 col-md-4 text-muted fw-semibold">
                                Ende
                            </div>
                            <div class="col-12 col-md-8">
                                <?= htmlspecialchars(
                                    (string) ($schedule['end_at'] ?? '')
                                ) ?>
                            </div>
                        </div>

                        <div class="row g-0 border-bottom px-3 py-2">
                            <div class="col-12 col-md-4 text-muted fw-semibold">
                                Intervall
                            </div>
                            <div class="col-12 col-md-8">

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

                        <div class="row g-0 px-3 py-2">
                            <div class="col-12 col-md-4 text-muted fw-semibold">
                                Nachts deaktivieren
                            </div>
                            <div class="col-12 col-md-8">
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

            <!-- Aktionen -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

                <a
                    href="/?page=measure-wizard&step=3"
                    class="btn btn-secondary"
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

                    <button type="submit" class="btn btn-success">
                        Maßnahme erstellen
                    </button>

                </form>

            </div>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
