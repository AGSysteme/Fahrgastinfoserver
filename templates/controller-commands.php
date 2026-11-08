<?php
/**
 * @var array $controller
 * @var list<array<string, mixed>> $commands
 * @var string|null $error
 * @var string $csrfToken
 */

$statusLabels = [
    'pending' => ['wartet', 'text-muted'],
    'sent' => ['gesendet', 'text-warning'],
    'started' => ['läuft', 'text-warning'],
    'done' => ['fertig', 'text-success'],
    'error' => ['Fehler', 'text-danger'],
];
?>
<?php $pageTitle = 'Befehle – ' . $controller['name']; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1000px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="h4 mb-1">Befehle</h2>
                    <div class="text-muted small">
                        Controller:
                        <strong><?= htmlspecialchars((string) $controller['name']) ?></strong>
                        · Geräte-ID <?= htmlspecialchars((string) $controller['device_id']) ?>
                        · <?= $controller['transport'] === 'rs485' ? 'RS485' : 'LAN' ?>
                    </div>
                </div>
                <a class="btn btn-secondary" href="/?page=controllers">← Controller</a>
            </div>

            <!-- Aktionen -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <form method="post">
                    <input type="hidden" name="page" value="controllers">
                    <input type="hidden" name="action" value="test-play">
                    <input type="hidden" name="controller_id" value="<?= (int) $controller['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="btn btn-primary">Test abspielen</button>
                </form>

                <form method="post">
                    <input type="hidden" name="page" value="controllers">
                    <input type="hidden" name="action" value="stop">
                    <input type="hidden" name="controller_id" value="<?= (int) $controller['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="btn btn-danger">Stopp</button>
                </form>
            </div>

            <p class="text-muted small mb-3">
                Der Test spielt die Testdurchsage ohne Ausgänge ab. Die Seite aktualisiert sich alle 5 Sekunden.
            </p>

            <?php if ($commands === []): ?>
                <p class="text-muted mb-0">Noch keine Befehle an diesen Controller.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Zeit (UTC)</th>
                                <th>Befehl</th>
                                <th>Ausgänge</th>
                                <th>Status</th>
                                <th>Hinweis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commands as $command): ?>
                                <?php
                                $payload = json_decode((string) $command['payload'], true) ?: [];
                                [$label, $class] = $statusLabels[(string) $command['status']] ?? [(string) $command['status'], ''];
                                ?>
                                <tr>
                                    <td class="small"><?= htmlspecialchars((string) $command['created_at']) ?></td>
                                    <td>
                                        <?= $command['command'] === 'play'
                                            ? 'Ansage ' . (int) ($payload['audio'] ?? 0)
                                            : 'Stopp' ?>
                                    </td>
                                    <td class="small">
                                        <?= ($payload['pins'] ?? []) !== []
                                            ? htmlspecialchars(implode(', ', $payload['pins']))
                                            : '–' ?>
                                    </td>
                                    <td class="<?= $class ?>"><?= htmlspecialchars($label) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars((string) ($command['detail'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>

</main>

<script>
// Quittungen laufend aktualisieren
setTimeout(() => location.reload(), 5000);
</script>
</body>
</html>
