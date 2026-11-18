<?php
/**
 * @var array $controller
 * @var string|null $log
 * @var string|null $logError
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = 'Logfile – ' . $controller['name']; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1100px;">

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="h4 mb-1">Logfile</h2>
                    <div class="text-muted small">
                        Controller:
                        <strong><?= htmlspecialchars((string) $controller['name']) ?></strong>
                        · IP <?= htmlspecialchars((string) ($controller['ip_address'] ?? '–')) ?>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="/?page=controllers&action=logfile&id=<?= (int) $controller['id'] ?>">Neu laden</a>
                    <a class="btn btn-secondary" href="/?page=controllers">← Controller</a>
                </div>
            </div>

            <?php if ($logError !== null): ?>
                <div class="alert alert-danger mb-0"><?= htmlspecialchars($logError) ?></div>
            <?php elseif ($log === '' || $log === null): ?>
                <p class="text-muted mb-0">Die Logdatei ist leer.</p>
            <?php else: ?>
                <pre class="mb-0 p-3 rounded border bg-body-tertiary" style="white-space: pre-wrap; max-height: 70vh; overflow: auto; font-size: .85rem;"><?= htmlspecialchars($log) ?></pre>
            <?php endif; ?>

        </div>
    </div>

</main>

</body>
</html>
