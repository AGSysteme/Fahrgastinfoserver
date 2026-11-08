<?php
/**
 * @var array $measures
 * @var string $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Maßnahmen – ELA Server</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>

<body>

<header class="py-4 px-4 px-md-5">
    <h1 class="h3 mb-0">ELA Server</h1>
</header>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1200px;">

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div>
                    <h2 class="h4 mb-1">Maßnahmen</h2>

                    <div class="text-muted small">
                        <?= count($measures) ?>
                        Maßnahme<?= count($measures) === 1 ? '' : 'n' ?>
                    </div>
                </div>

                <a
                    href="/?page=measure-wizard&step=1&new=1"
                    class="btn btn-primary"
                >
                    + Neue Maßnahme
                </a>

            </div>

            <!-- Liste -->
            <?php if (!$measures): ?>

                <div class="text-center text-muted py-5 border border-2 border-dashed rounded-3">
                    Noch keine Maßnahmen vorhanden.
                </div>

            <?php else: ?>

                <div class="d-flex flex-column gap-3">

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
                            $statusClass = 'text-secondary';

                        } elseif (
                            $startTimestamp !== false &&
                            $startTimestamp > $now
                        ) {

                            $statusText = 'Geplant';
                            $statusClass = 'text-warning';

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
                            $statusClass = 'text-secondary';

                        } else {

                            $statusText = 'Aktiv';
                            $statusClass = 'text-success';

                        }

                        ?>

                        <div class="card">
                            <div class="card-body p-3 p-md-4">

                                <div class="row g-3 align-items-center">

                                    <div class="col-12 col-md">

                                        <div class="fw-bold small mb-1 <?= $statusClass ?>">
                                            <?= htmlspecialchars($statusText) ?>
                                        </div>

                                        <div class="fs-5 fw-bold text-break">
                                            <?= htmlspecialchars(
                                                (string) $measure['name']
                                            ) ?>
                                        </div>

                                        <div class="fw-semibold mt-2 text-break">
                                            🔊
                                            <?= htmlspecialchars(
                                                (string) $measure['audio_text']
                                            ) ?>
                                        </div>

                                        <div class="text-muted small mt-2 lh-lg">

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

                                    <div class="col-12 col-md-auto">

                                        <div class="d-flex flex-wrap gap-2">

                                            <a
                                                href="/?page=measure-edit&id=<?= (int) $measure['id'] ?>"
                                                class="btn btn-sm btn-primary"
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

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Löschen
                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <!-- Zurück -->
            <div class="mt-4">
                <a href="/" class="text-decoration-none">
                    &larr; Zurück zum Dashboard
                </a>
            </div>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
