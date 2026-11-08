<?php
/**
 * @var array $wizard
 * @var array $audio
 * @var list<int> $selected
 * @var array $wizardLines  je Linie: number, name, items, ids, open
 * @var array $otherItems   Haltestellen ohne Linie
 * @var bool $hasStations
 * @var string|null $error
 * @var string $csrfToken
 */

use Ela\Web\StationPicker;
?>
<?php $pageTitle = 'Maßnahme – Haltestellen auswählen'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1000px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">Haltestellen auswählen</h2>
                <div class="text-muted small">Schritt 2 von 4</div>
            </div>

            <!-- Maßnahmen-Info -->
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="fw-semibold"><?= htmlspecialchars((string) $wizard['name']) ?></div>
                    <div class="text-muted small mt-1"><?= htmlspecialchars((string) $audio['text']) ?></div>
                </div>
            </div>

            <form method="post" action="/?page=measure-wizard&step=2" id="targets-form">

                <input type="hidden" name="page" value="measure-wizard">
                <input type="hidden" name="action" value="measure-wizard-step-2">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <!-- Suche -->
                <div class="mb-4">
                    <label for="station-search" class="form-label">Haltestelle suchen</label>
                    <input id="station-search" type="search" class="form-control search-orange" placeholder="Name der Haltestelle">
                    <p id="no-results" class="text-muted small mt-2 mb-0 d-none">Keine Haltestelle gefunden.</p>
                </div>

                <!-- Linien -->
                <h3 class="h5 mb-1">Linien <span id="count" class="text-muted small" aria-live="polite"></span></h3>
                <p class="text-muted small mb-3">
                    Eine Linie auswählen, um alle ihre Haltestellen zu übernehmen. Einzelne Haltestellen lassen sich danach per Drag and Drop verschieben.
                </p>

                <div class="list-group mb-4">
                    <?php foreach ($wizardLines as $line): ?>
                        <div class="list-group-item p-0 line-item <?= $line['open'] ? 'open' : '' ?>">

                            <div class="d-flex align-items-center gap-3 px-3 py-3">
                                <label class="d-flex align-items-center gap-3 flex-grow-1 mb-0 <?= $line['ids'] === [] ? 'text-muted' : '' ?>" style="cursor: pointer;">
                                    <input
                                        type="checkbox"
                                        class="form-check-input m-0 flex-shrink-0 line-toggle"
                                        data-stations="<?= htmlspecialchars(json_encode($line['ids']), ENT_QUOTES) ?>"
                                        <?= $line['ids'] === [] ? 'disabled' : '' ?>
                                    >
                                    <span class="fw-semibold">Linie <?= htmlspecialchars((string) $line['number']) ?></span>
                                    <span><?= htmlspecialchars((string) ($line['name'] ?? '')) ?></span>
                                    <small class="text-muted ms-auto line-count"></small>
                                </label>
                            </div>

                            <div class="line-panel border-top bg-body-tertiary px-3 py-3 <?= $line['open'] ? '' : 'd-none' ?>">
                                <?= StationPicker::render($line['items'], $selected) ?>
                            </div>

                        </div>
                    <?php endforeach; ?>

                    <?php if ($wizardLines === []): ?>
                        <div class="list-group-item text-muted">Es sind noch keine Linien vorhanden.</div>
                    <?php endif; ?>
                </div>

                <!-- Haltestellen ohne Linie -->
                <?php if ($otherItems !== []): ?>
                    <h3 id="other-title" class="h5 mb-1">Weitere Haltestellen</h3>
                    <p id="other-hint" class="text-muted small mb-3">Haltestellen, die keiner Linie zugeordnet sind.</p>

                    <div id="other-stations" class="mb-4">
                        <?= StationPicker::render($otherItems, $selected) ?>
                    </div>
                <?php endif; ?>

                <?php if (!$hasStations): ?>
                    <p class="text-muted">Es sind noch keine Haltestellen vorhanden.</p>
                <?php endif; ?>

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
                    <a class="btn btn-secondary" href="/?page=measure-wizard&step=1">← Zurück</a>
                    <button type="submit" class="btn btn-primary">Weiter →</button>
                </div>

            </form>

        </div>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/measure-wizard-step-2.js"></script>
</body>
</html>
