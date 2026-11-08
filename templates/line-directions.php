<?php
/**
 * @var array $line
 * @var array $directions
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Richtungen – <?= htmlspecialchars((string) $line['number']) ?> – ELA Server</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>

<body>

<header class="py-4 px-4 px-md-5">
    <h1 class="h3 mb-0">ELA Server</h1>
</header>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1000px;">

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift und neue Richtung -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div>
                    <h2 class="h4 mb-1">
                        Linie <?= htmlspecialchars((string) $line['number']) ?>
                    </h2>

                    <div class="text-muted small">
                        <?= htmlspecialchars((string) ($line['name'] ?? '')) ?>
                    </div>
                </div>

                <a
                    class="btn btn-primary"
                    href="/?page=lines&action=direction-new&id=<?= (int) $line['id'] ?>"
                >
                    + Neue Richtung
                </a>

            </div>

            <!-- Fehlermeldung -->
            <?php if ($error !== null): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Richtungen -->
            <?php if (!$directions): ?>

                <div class="alert alert-secondary mb-0">
                    Für diese Linie wurden noch keine Richtungen angelegt.
                </div>

            <?php else: ?>

                <div class="row g-3">

                    <?php foreach ($directions as $direction): ?>

                        <div class="col-12">

                            <div class="card">
                                <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">

                                    <div class="flex-grow-1">

                                        <div class="fs-5 fw-semibold text-break">
                                            <?= htmlspecialchars((string) $direction['name']) ?>
                                        </div>

                                        <div class="text-muted small">
                                            Ziel:
                                            <?= htmlspecialchars((string) $direction['destination']) ?>
                                        </div>

                                        <div class="mt-1">
                                            <?php if ((int) $direction['active'] === 1): ?>
                                                <span class="badge text-bg-success">Aktiv</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-danger">Inaktiv</span>
                                            <?php endif; ?>
                                        </div>

                                    </div>

                                    <div class="d-flex flex-wrap gap-2">

                                        <a
                                            class="btn btn-sm btn-primary"
                                            href="/?page=lines&action=direction-edit&id=<?= (int) $direction['id'] ?>"
                                        >
                                            Bearbeiten
                                        </a>

                                        <a
                                            class="btn btn-sm btn-secondary"
                                            href="/?page=lines&action=direction-stations&id=<?= (int) $direction['id'] ?>"
                                        >
                                            Haltestellen
                                        </a>

                                        <form
                                            method="post"
                                            onsubmit="return confirm('Diese Richtung wirklich löschen?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="page"
                                                value="lines"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="direction-delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $direction['id'] ?>"
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

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <!-- Zurück -->
            <div class="mt-4">
                <a href="/?page=lines" class="text-decoration-none">
                    &larr; Zurück zu den Linien
                </a>
            </div>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
