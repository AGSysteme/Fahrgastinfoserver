<?php

declare(strict_types=1);

$wizard =
    $_SESSION['measure_wizard']
        ?? [];

$selectedAudioId =
    !empty($wizard['audio_id'])
        ? (int) $wizard['audio_id']
        : (
            isset($_GET['audio_id'])
                ? (int) $_GET['audio_id']
                : 0
        );

$wizardName =
    (string) (
        $wizard['name']
            ?? ''
    );

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
                    Schritt 1 von 2 – Maßnahme und Ansage festlegen
                </div>
            </div>

            <?php if (!$audioFiles): ?>

                <div class="alert alert-secondary">
                    <div class="mb-2">Es sind noch keine Ansagen vorhanden.</div>
                    <div>Bitte zuerst eine Ansage erzeugen.</div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="/?page=audio" class="btn btn-primary">
                        Zu den Ansagen
                    </a>
                </div>

            <?php else: ?>

                <form method="post">

                    <input
                        type="hidden"
                        name="page"
                        value="measure-wizard"
                    >

                    <input
                        type="hidden"
                        name="action"
                        value="measure-wizard-step-1"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($csrfToken) ?>"
                    >

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
                                required
                                value="<?= htmlspecialchars($wizardName) ?>"
                                placeholder="z. B. Baustelle Hauptbahnhof"
                            >
                        </div>

                        <div class="col-12">
                            <label for="audio_id" class="form-label">
                                Ansage <span class="text-danger">*</span>
                            </label>

                            <select
                                id="audio_id"
                                name="audio_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Bitte Ansage auswählen
                                </option>

                                <?php foreach ($audioFiles as $audio): ?>

                                    <?php
                                    $audioId =
                                        (int) $audio['id'];

                                    $selected =
                                        $audioId === $selectedAudioId;
                                    ?>

                                    <option
                                        value="<?= $audioId ?>"
                                        <?= $selected ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            (string) $audio['text']
                                        ) ?>
                                        –
                                        <?= htmlspecialchars(
                                            ucfirst(
                                                (string) $audio['voice']
                                            )
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <div class="form-text">
                                Eine Maßnahme kann genau einer Ansage zugeordnet werden.
                            </div>
                        </div>

                    </div>

                    <!-- Aktionen -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

                        <a
                            href="/?page=measures"
                            class="btn btn-secondary"
                        >
                            Abbrechen
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Weiter →
                        </button>

                    </div>

                </form>

            <?php endif; ?>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
