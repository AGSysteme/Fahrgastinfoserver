<?php
declare(strict_types=1);
$selected = array_map('intval', $savedTargets);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error !== null && is_array($_POST['stations'] ?? null)) {
    $selected = array_map('intval', array_filter($_POST['stations'], 'is_scalar'));
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maßnahme – Haltestellen auswählen</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; background: #f4f6f8; color: #222; font-family: Arial, sans-serif; }
main { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
.card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px #0001; }
.info { background: #f3f5f8; padding: 16px; border-radius: 8px; }
.error { background: #fee2e2; color: #991b1b; padding: 12px; }
.line-options { display: flex; flex-wrap: wrap; gap: 12px; margin: 16px 0; }
.line-options label { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
#search { width: 100%; padding: 12px; margin: 12px 0; border: 1px solid #ccc; border-radius: 6px; }
.station { display: flex; gap: 12px; padding: 12px; border-bottom: 1px solid #eee; }
.station[hidden] { display: none; }
.actions { display: flex; justify-content: space-between; gap: 12px; margin-top: 24px; }
.button, button { background: #2563eb; color: white; padding: 12px 18px; border: 0; border-radius: 7px; text-decoration: none; cursor: pointer; font-size: 16px; }
.secondary { background: #6b7280; }
</style>
</head>
<body><main><div class="card">
<h1>Haltestellen auswählen</h1>
<p>Schritt 2 von 4</p>
<div class="info"><strong><?= htmlspecialchars((string) $wizard['name']) ?></strong><p><?= htmlspecialchars((string) $audio['text']) ?></p></div>
<?php if ($error !== null): ?><p class="error" role="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post" action="/?page=measure-wizard&step=2">
<input type="hidden" name="page" value="measure-wizard">
<input type="hidden" name="action" value="measure-wizard-step-2">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
<h2>Über Linien auswählen</h2>
<p>Eine Linie wählt ihre Haltestellen gemeinsam aus. Haltestellen mehrerer Linien werden nur einmal angesprochen.</p>
<div class="line-options">
<?php foreach ($wizardLines as $line): ?>
<?php $ids = array_map('intval', array_column($line['stations'], 'station_id')); ?>
<label><input type="checkbox" class="line-toggle" data-stations="<?= htmlspecialchars(json_encode($ids), ENT_QUOTES) ?>" <?= !$ids ? 'disabled' : '' ?>> Linie <?= htmlspecialchars((string) $line['number']) ?> <?= htmlspecialchars((string) ($line['name'] ?? '')) ?></label>
<?php endforeach; ?>
</div>
<h2>Haltestellen <span id="count" aria-live="polite"></span></h2>
<label for="search">Haltestellen suchen</label>
<input id="search" type="search" placeholder="Name, Kürzel oder Kennung">
<p><button type="button" id="select-visible">Sichtbare auswählen</button> <button type="button" id="clear" class="secondary">Auswahl aufheben</button></p>
<?php foreach ($stations as $station): ?>
<label class="station">
<input type="checkbox" name="stations[]" value="<?= (int) $station['id'] ?>" <?= in_array((int) $station['id'], $selected, true) ? 'checked' : '' ?>>
<span><?= htmlspecialchars((string) $station['name']) ?> <small><?= htmlspecialchars((string) ($station['short_name'] ?? '')) ?> <?= htmlspecialchars((string) ($station['station_code'] ?? '')) ?><?= (int) $station['active'] !== 1 ? ' (inaktiv)' : '' ?></small></span>
</label>
<?php endforeach; ?>
<?php if (!$stations): ?><p>Es sind noch keine Haltestellen vorhanden.</p><?php endif; ?>
<div class="actions"><a class="button secondary" href="/?page=measure-wizard&step=1">← Zurück</a><button type="submit">Weiter →</button></div>
</form>
</div></main>
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
</body></html>
