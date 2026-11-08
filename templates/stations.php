
<?php
/**
 * @var array $stations
 * @var string $search
 */
?>
<?php $pageTitle = 'Haltestellen'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1240px;">

    <div class="card shadow-sm">

        <div class="card-body p-3 p-md-4">

            <!-- Überschrift und neue Haltestelle -->

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <h2 class="h4 mb-0">Haltestellen</h2>

                <a
                    class="btn btn-primary"
                    href="/?page=stations&action=new"
                >
                    + Neue Haltestelle
                </a>

            </div>

            <!-- Suchfunktion -->

            <form method="get" class="row g-2 mb-4">

                <input
                    type="hidden"
                    name="page"
                    value="stations"
                >

                <div class="col">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="<?= htmlspecialchars((string) $search) ?>"
                        placeholder="Haltestelle suchen..."
                        aria-label="Haltestelle suchen"
                    >
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">
                        Suchen
                    </button>
                </div>

            </form>

            <!-- Keine Ergebnisse -->

            <?php if (!$stations): ?>

                <div class="text-center py-5">

                    <h3 class="h5 mb-2">
                        Keine Haltestellen gefunden
                    </h3>

                    <p class="text-muted mb-0">
                        Es wurden keine passenden Haltestellen gefunden.
                    </p>

                </div>

            <?php else: ?>

                <!-- Haltestellentabelle -->

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Haltestelle</th>
                                <th>Koordinaten</th>
                                <th>Status</th>
                                <th class="text-end">Aktionen</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($stations as $station): ?>

                            <tr>

                                <!-- Haltestellencode -->

                                <td>
                                    <span class="font-monospace text-muted small">
                                        <?= htmlspecialchars(
                                            (string) ($station['station_code'] ?? '–')
                                        ) ?>
                                    </span>
                                </td>

                                <!-- Name und Kurzname -->

                                <td>

                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            (string) $station['name']
                                        ) ?>
                                    </div>

                                    <?php if ($station['short_name'] !== null): ?>

                                        <div class="text-muted small mt-1">
                                            <?= htmlspecialchars(
                                                (string) $station['short_name']
                                            ) ?>
                                        </div>

                                    <?php endif; ?>

                                </td>

                                <!-- Koordinaten -->

                                <td>

                                    <?php if (
                                        $station['latitude'] !== null &&
                                        $station['longitude'] !== null
                                    ): ?>

                                        <div class="font-monospace small">
                                            <?= htmlspecialchars(
                                                (string) $station['latitude']
                                            ) ?>
                                        </div>

                                        <div class="font-monospace small text-muted">
                                            <?= htmlspecialchars(
                                                (string) $station['longitude']
                                            ) ?>
                                        </div>

                                    <?php else: ?>

                                        <span class="text-muted">–</span>

                                    <?php endif; ?>

                                </td>

                                <!-- Status -->

                                <td>

                                    <?php if ((int) $station['active'] === 1): ?>

                                        <span class="badge text-bg-success">
                                            Aktiv
                                        </span>

                                    <?php else: ?>

                                        <span class="badge text-bg-danger">
                                            Inaktiv
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <!-- Aktionen -->

                                <td class="text-end">

                                    <div class="d-flex justify-content-end gap-2 flex-wrap">

                                        <a
                                            class="btn btn-sm btn-primary"
                                            href="/?page=stations&action=edit&id=<?= (int) $station['id'] ?>"
                                        >
                                            Bearbeiten
                                        </a>

                                        <a
                                            class="btn btn-sm btn-danger"
                                            href="/?page=stations&action=delete&id=<?= (int) $station['id'] ?>"
                                            onclick="return confirm('Haltestelle wirklich löschen?');"
                                        >
                                            Löschen
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

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
