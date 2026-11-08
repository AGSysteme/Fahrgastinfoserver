
<?php
/**
 * @var array $controllers
 * @var string $search
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = 'Controller'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1240px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">

        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div>
                    <h2 class="h4 mb-1">Controller</h2>

                    <div class="text-muted small">
                        Verwaltung der angeschlossenen ELA-Controller
                    </div>
                </div>

                <a
                    class="btn btn-primary"
                    href="/?page=controllers&action=new"
                >
                    + Neuer Controller
                </a>

            </div>

            <!-- Suche -->

            <form method="get" class="row g-2 mb-4">

                <input
                    type="hidden"
                    name="page"
                    value="controllers"
                >

                <div class="col">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Controller suchen..."
                        aria-label="Controller suchen"
                    >
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        Suchen
                    </button>
                </div>

                <?php if ($search !== ''): ?>

                    <div class="col-auto">
                        <a
                            href="/?page=controllers"
                            class="btn btn-secondary"
                        >
                            Zurücksetzen
                        </a>
                    </div>

                <?php endif; ?>

            </form>


            <!-- Keine Controller -->

            <?php if (!$controllers): ?>

                <div class="text-center py-5">

                    <div class="mb-3 text-muted">
                        <span class="fs-1">⌁</span>
                    </div>

                    <?php if ($search !== ''): ?>

                        <h3 class="h5 mb-2">
                            Keine Controller gefunden
                        </h3>

                        <p class="text-muted mb-0">
                            Für
                            <strong>
                                „<?= htmlspecialchars($search) ?>“
                            </strong>
                            wurden keine passenden Controller gefunden.
                        </p>

                    <?php else: ?>

                        <h3 class="h5 mb-2">
                            Noch keine Controller vorhanden
                        </h3>

                        <p class="text-muted mb-0">
                            Lege deinen ersten ELA-Controller an.
                        </p>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <!-- Controller-Karten -->

                <div class="row g-3">

                    <?php foreach ($controllers as $controller): ?>

                        <div class="col-12 col-lg-6">

                            <div class="card h-100 controller-card">

                                <div class="card-body p-3 p-md-4">

                                    <!-- Controller-Name und Status -->

                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">

                                        <h3 class="h5 mb-0 text-break">
                                            <?= htmlspecialchars(
                                                (string) $controller['name']
                                            ) ?>
                                        </h3>

                                        <?php if ((int) $controller['enabled'] === 1): ?>

                                            <span class="badge text-bg-success flex-shrink-0">
                                                Aktiv
                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-danger flex-shrink-0">
                                                Inaktiv
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <!-- Technische Details -->

                                    <div class="row g-2 mb-4">

                                        <div class="col-12 col-sm-6">

                                            <div class="controller-detail h-100">

                                                <div class="text-muted small mb-1">
                                                    Geräte-ID
                                                </div>

                                                <div class="fw-semibold font-monospace text-break">
                                                    <?= htmlspecialchars(
                                                        (string) $controller['device_id']
                                                    ) ?>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-12 col-sm-6">

                                            <div class="controller-detail h-100">

                                                <div class="text-muted small mb-1">
                                                    IP-Adresse
                                                </div>

                                                <div class="fw-semibold font-monospace text-break">
                                                    <?= htmlspecialchars(
                                                        (string) (
                                                            $controller['ip_address'] ?? '–'
                                                        )
                                                    ) ?>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-12 col-sm-6">

                                            <div class="controller-detail h-100">

                                                <div class="text-muted small mb-1">
                                                    MQTT Client-ID
                                                </div>

                                                <div class="fw-semibold text-break">
                                                    <?= htmlspecialchars(
                                                        (string) $controller['mqtt_client_id']
                                                    ) ?>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-12 col-sm-6">

                                            <div class="controller-detail h-100">

                                                <div class="text-muted small mb-1">
                                                    MQTT Topic
                                                </div>

                                                <div class="fw-semibold text-break">
                                                    <?= htmlspecialchars(
                                                        (string) $controller['mqtt_topic']
                                                    ) ?>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- Aktionen -->

                                    <div class="d-flex flex-wrap gap-2">

                                        <a
                                            class="btn btn-sm btn-primary"
                                            href="/?page=controllers&action=stations&id=<?= (int) $controller['id'] ?>"
                                        >
                                            Haltestellen
                                        </a>

                                        <a
                                            class="btn btn-sm btn-secondary"
                                            href="/?page=controllers&action=edit&id=<?= (int) $controller['id'] ?>"
                                        >
                                            Bearbeiten
                                        </a>

                                        <form
                                            method="post"
                                            onsubmit="return confirm(<?= htmlspecialchars(
                                                json_encode(
                                                    'Controller „' . (string) $controller['name'] . '“ wirklich löschen?',
                                                    JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>);"
                                        >

                                            <input
                                                type="hidden"
                                                name="page"
                                                value="controllers"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $controller['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($csrfToken) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
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

            <!-- Zurück zum Dashboard -->

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
