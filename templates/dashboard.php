<?php $pageTitle = ''; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1240px;">

    <!-- Kennzahlen -->

    <div class="row g-3 g-md-4 mb-4">

        <div class="col-6 col-lg">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-body-secondary">Haltestellen</div>
                    <div class="fs-2 fw-bold mt-1"><?= $stationsCount ?></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-body-secondary">Linien</div>
                    <div class="fs-2 fw-bold mt-1"><?= $linesCount ?></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-body-secondary">Controller</div>
                    <div class="fs-2 fw-bold mt-1"><?= $controllersCount ?></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-body-secondary">Audio-Dateien</div>
                    <div class="fs-2 fw-bold mt-1"><?= $audioFilesCount ?></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-body-secondary">Maßnahmen</div>
                    <div class="fs-2 fw-bold mt-1"><?= $measuresCount ?></div>

                    <div class="row g-0 text-center mt-2">
                        <div class="col">
                            <div class="fs-5 fw-bold text-success"><?= $measuresActiveCount ?></div>
                            <div class="small text-body-secondary">Aktiv</div>
                        </div>
                        <div class="col">
                            <div class="fs-5 fw-bold text-primary"><?= $measuresPlannedCount ?></div>
                            <div class="small text-body-secondary">Geplant</div>
                        </div>
                        <div class="col">
                            <div class="fs-5 fw-bold"><?= $measuresExpiredCount ?></div>
                            <div class="small text-body-secondary">Abgelaufen</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Navigation -->

    <div class="card shadow-sm">

        <div class="card-body p-3 p-md-4">

            <h2 class="h5 mb-3">Verwaltung</h2>

            <div class="row g-2 g-md-3">

                <div class="col-12 col-sm-6 col-lg">
                    <a class="btn btn-primary w-100 py-3 fw-semibold" href="/?page=stations">Haltestellen</a>
                </div>

                <div class="col-12 col-sm-6 col-lg">
                    <a class="btn btn-primary w-100 py-3 fw-semibold" href="/?page=lines">Linien</a>
                </div>

                <div class="col-12 col-sm-6 col-lg">
                    <a class="btn btn-primary w-100 py-3 fw-semibold" href="/?page=controllers">Controller</a>
                </div>

                <div class="col-12 col-sm-6 col-lg">
                    <a class="btn btn-primary w-100 py-3 fw-semibold" href="/?page=audio">Ansagen</a>
                </div>

                <div class="col-12 col-sm-6 col-lg">
                    <a class="btn btn-primary w-100 py-3 fw-semibold" href="/?page=measures">Maßnahmen</a>
                </div>

            </div>

        </div>

    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
