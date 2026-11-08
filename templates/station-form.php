<?php
/**
 * @var array|null $station
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = $station ? 'Haltestelle bearbeiten' : 'Neue Haltestelle'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 850px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">
                    <?= $station ? 'Haltestelle bearbeiten' : 'Neue Haltestelle' ?>
                </h2>

                <div class="text-muted small">
                    <?php if ($station): ?>
                        Haltestellendaten bearbeiten
                    <?php else: ?>
                        Neue Haltestelle im System anlegen
                    <?php endif; ?>
                </div>
            </div>


            <form method="post">

                <input type="hidden" name="page" value="stations">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken) ?>"
                >

                <input
                    type="hidden"
                    name="action"
                    value="<?= $station ? 'update' : 'create' ?>"
                >

                <?php if ($station): ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $station['id'] ?>"
                    >
                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-12">
                        <label for="name" class="form-label">
                            Name <span class="text-danger">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="form-control"
                            required
                            maxlength="200"
                            placeholder="z. B. Hauptbahnhof"
                            value="<?= htmlspecialchars(
                                (string) ($station['name'] ?? '')
                            ) ?>"
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="short_name" class="form-label">
                            Kurzname
                        </label>

                        <input
                            id="short_name"
                            type="text"
                            name="short_name"
                            class="form-control"
                            maxlength="100"
                            placeholder="z. B. Hbf"
                            value="<?= htmlspecialchars(
                                (string) ($station['short_name'] ?? '')
                            ) ?>"
                        >

                        <div class="form-text">
                            Kurze Bezeichnung für Anzeigen.
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="station_code" class="form-label">
                            Haltestellencode
                        </label>

                        <input
                            id="station_code"
                            type="text"
                            name="station_code"
                            class="form-control"
                            maxlength="50"
                            placeholder="z. B. HST001"
                            value="<?= htmlspecialchars(
                                (string) ($station['station_code'] ?? '')
                            ) ?>"
                        >

                        <div class="form-text">
                            Eindeutiger interner Haltestellencode.
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="latitude" class="form-label">
                            Breitengrad
                        </label>

                        <input
                            id="latitude"
                            type="number"
                            name="latitude"
                            class="form-control"
                            step="any"
                            placeholder="z. B. 48.3659"
                            value="<?= htmlspecialchars(
                                (string) ($station['latitude'] ?? '')
                            ) ?>"
                        >

                        <div class="form-text">
                            Geografische Position der Haltestelle.
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="longitude" class="form-label">
                            Längengrad
                        </label>

                        <input
                            id="longitude"
                            type="number"
                            name="longitude"
                            class="form-control"
                            step="any"
                            placeholder="z. B. 10.8985"
                            value="<?= htmlspecialchars(
                                (string) ($station['longitude'] ?? '')
                            ) ?>"
                        >

                        <div class="form-text">
                            Geografische Position der Haltestelle.
                        </div>
                    </div>

                    <?php if ($station): ?>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="form-check form-switch mb-0">
                                    <input
                                        id="active"
                                        type="checkbox"
                                        name="active"
                                        value="1"
                                        class="form-check-input"
                                        role="switch"
                                        <?= (int) $station['active'] === 1
                                            ? 'checked'
                                            : '' ?>
                                    >

                                    <label for="active" class="form-check-label">
                                        Haltestelle ist aktiv
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-4 border-top">

                    <a
                        class="btn btn-secondary"
                        href="/?page=stations"
                    >
                        ← Abbrechen
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <?= $station
                            ? 'Änderungen speichern'
                            : 'Haltestelle anlegen' ?>
                    </button>

                </div>

            </form>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
