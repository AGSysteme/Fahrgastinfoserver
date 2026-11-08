<?php
declare(strict_types=1);
$selected = array_map('intval', $savedTargets);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error !== null && is_array($_POST['stations'] ?? null)) {
    $selected = array_map('intval', array_filter($_POST['stations'], 'is_scalar'));
}
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


            <form method="post" action="/?page=measure-wizard&step=2">

                <input type="hidden" name="page" value="measure-wizard">
                <input type="hidden" name="action" value="measure-wizard-step-2">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <!-- Auswahl über Linien -->
                <h3 class="h5 mb-1">Über Linien auswählen</h3>
                <p class="text-muted small mb-3">
                    Eine Linie wählt ihre Haltestellen gemeinsam aus. Haltestellen mehrerer Linien werden nur einmal angesprochen.
                </p>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php foreach ($wizardLines as $line): ?>
                        <?php $ids = array_map('intval', array_column($line['stations'], 'station_id')); ?>
                        <label class="btn btn-outline-secondary btn-sm mb-0 <?= !$ids ? 'disabled' : '' ?>">
                            <input
                                type="checkbox"
                                class="form-check-input line-toggle me-1"
                                data-stations="<?= htmlspecialchars(json_encode($ids), ENT_QUOTES) ?>"
                                <?= !$ids ? 'disabled' : '' ?>
                            >
                            Linie <?= htmlspecialchars((string) $line['number']) ?>
                            <?= htmlspecialchars((string) ($line['name'] ?? '')) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- Haltestellen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <h3 class="h5 mb-0">
                        Haltestellen <span id="count" class="text-muted small" aria-live="polite"></span>
                    </h3>
                </div>

                <label for="search" class="form-label">Haltestellen suchen</label>
                <input
                    id="search"
                    type="search"
                    class="form-control mb-3"
                    placeholder="Name, Kürzel oder Kennung"
                >

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" id="select-visible" class="btn btn-sm btn-primary">Sichtbare auswählen</button>
                    <button type="button" id="clear" class="btn btn-sm btn-secondary">Auswahl aufheben</button>
                </div>

                <div class="list-group mb-3">
                    <?php foreach ($stations as $station): ?>
                        <label class="list-group-item d-flex align-items-start gap-3">
                            <input
                                type="checkbox"
                                class="form-check-input mt-1 flex-shrink-0"
                                name="stations[]"
                                value="<?= (int) $station['id'] ?>"
                                <?= in_array((int) $station['id'], $selected, true) ? 'checked' : '' ?>
                            >
                            <span>
                                <span class="fw-semibold"><?= htmlspecialchars((string) $station['name']) ?></span>
                                <small class="text-muted">
                                    <?= htmlspecialchars((string) ($station['short_name'] ?? '')) ?>
                                    <?= htmlspecialchars((string) ($station['station_code'] ?? '')) ?>
                                    <?= (int) $station['active'] !== 1 ? ' (inaktiv)' : '' ?>
                                </small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php if (!$stations): ?>
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
<script>
const stations = [...document.querySelectorAll('input[name="stations[]"]')];
const lines = [...document.querySelectorAll('.line-toggle')];
function update() {
    const selected = new Set(stations.filter(s => s.checked).map(s => Number(s.value)));
    document.getElementById('count').textContent = `(${selected.size} ausgewählt)`;
    lines.forEach(line => {
        const ids = JSON.parse(line.dataset.stations);
        const count = ids.filter(id => selected.has(id)).length;
        line.checked = ids.length > 0 && count === ids.length;
        line.indeterminate = count > 0 && count < ids.length;
    });
}
lines.forEach(line => line.addEventListener('change', () => {
    const ids = new Set(JSON.parse(line.dataset.stations));
    stations.forEach(s => { if (ids.has(Number(s.value))) s.checked = line.checked; });
    update();
}));
stations.forEach(s => s.addEventListener('change', update));
document.getElementById('search').addEventListener('input', event => {
    const query = event.target.value.trim().toLocaleLowerCase('de');
    stations.forEach(s => { s.closest('label').hidden = !s.closest('label').textContent.toLocaleLowerCase('de').includes(query); });
});
document.getElementById('select-visible').addEventListener('click', () => { stations.forEach(s => { if (!s.closest('label').hidden) s.checked = true; }); update(); });
document.getElementById('clear').addEventListener('click', () => { stations.forEach(s => s.checked = false); update(); });
update();
</script>
</body>
</html>
