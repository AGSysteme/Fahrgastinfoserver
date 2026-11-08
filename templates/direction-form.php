<?php
/**
 * @var array $line
 * @var array|null $direction
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $direction ? 'Richtung bearbeiten' : 'Neue Richtung' ?> – ELA Server</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>

<body>

<header class="py-4 px-4 px-md-5">
    <h1 class="h3 mb-0">ELA Server</h1>
</header>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 800px;">

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">
                    <?= $direction ? 'Richtung bearbeiten' : 'Neue Richtung' ?>
                </h2>

                <div class="text-muted small">
                    Linie
                    <strong>
                        <?= htmlspecialchars((string) $line['number']) ?>
                    </strong>
                    –
                    <?= htmlspecialchars((string) ($line['name'] ?? '')) ?>
                </div>
            </div>

            <!-- Fehlermeldung -->
            <?php if ($error !== null): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">

                <input type="hidden" name="page" value="lines">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken) ?>"
                >

                <?php if ($direction): ?>

                    <input
                        type="hidden"
                        name="action"
                        value="direction-update"
                    >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $direction['id'] ?>"
                    >

                <?php else: ?>

                    <input
                        type="hidden"
                        name="action"
                        value="direction-create"
                    >

                    <input
                        type="hidden"
                        name="line_id"
                        value="<?= (int) $line['id'] ?>"
                    >

                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-12">
                        <label for="name" class="form-label">
                            Bezeichnung <span class="text-danger">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="form-control"
                            required
                            maxlength="200"
                            value="<?= htmlspecialchars(
                                (string) ($direction['name'] ?? '')
                            ) ?>"
                        >
                    </div>

                    <div class="col-12">
                        <label for="destination" class="form-label">
                            Ziel
                        </label>

                        <input
                            id="destination"
                            type="text"
                            name="destination"
                            class="form-control"
                            maxlength="200"
                            value="<?= htmlspecialchars(
                                (string) ($direction['destination'] ?? '')
                            ) ?>"
                        >
                    </div>

                    <?php if ($direction): ?>
                        <div class="col-12">
                            <div class="form-check form-switch p-3 border rounded">
                                <input
                                    id="active"
                                    type="checkbox"
                                    name="active"
                                    value="1"
                                    class="form-check-input"
                                    role="switch"
                                    <?= (int) $direction['active'] === 1
                                        ? 'checked'
                                        : '' ?>
                                >

                                <label for="active" class="form-check-label">
                                    Aktiv
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

                    <a
                        class="btn btn-secondary"
                        href="/?page=lines&action=directions&id=<?= (int) $line['id'] ?>"
                    >
                        Abbrechen
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Speichern
                    </button>

                </div>

            </form>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
