<?php
/**
 * @var array|null $line
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = $line ? 'Linie bearbeiten' : 'Neue Linie'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 800px;">

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">
                    <?= $line ? 'Linie bearbeiten' : 'Neue Linie' ?>
                </h2>

                <div class="text-muted small">
                    <?= $line
                        ? 'Bezeichnung und Status der Linie bearbeiten.'
                        : 'Eine neue Linie anlegen.' ?>
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
                    name="action"
                    value="<?= $line ? 'update' : 'create' ?>"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken) ?>"
                >

                <?php if ($line): ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $line['id'] ?>"
                    >
                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-12 col-md-6">
                        <label for="number" class="form-label">
                            Liniennummer <span class="text-danger">*</span>
                        </label>

                        <input
                            id="number"
                            type="text"
                            name="number"
                            class="form-control"
                            maxlength="50"
                            required
                            value="<?= htmlspecialchars(
                                (string) ($line['number'] ?? '')
                            ) ?>"
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label">
                            Name
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="form-control"
                            maxlength="200"
                            placeholder="z. B. Linie 18"
                            value="<?= htmlspecialchars(
                                (string) ($line['name'] ?? '')
                            ) ?>"
                        >
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">
                            Beschreibung
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="form-control"
                            rows="4"
                        ><?= htmlspecialchars(
                            (string) ($line['description'] ?? '')
                        ) ?></textarea>
                    </div>

                    <?php if ($line): ?>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input
                                    id="active"
                                    type="checkbox"
                                    name="active"
                                    value="1"
                                    class="form-check-input"
                                    role="switch"
                                    <?= (int) $line['active'] === 1
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
                        href="/?page=lines"
                        class="btn btn-secondary"
                    >
                        &larr; Abbrechen
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
