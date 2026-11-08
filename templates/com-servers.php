<?php
/**
 * @var array $servers
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = 'COM-Server'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1000px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div>
                    <h2 class="h4 mb-1">COM-Server</h2>
                    <div class="text-muted small">
                        Verbindungen, denen Controller zugeordnet werden
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a class="btn btn-secondary" href="/?page=controllers">← Controller</a>
                    <a class="btn btn-primary" href="/?page=com-servers&action=new">+ Neuer COM-Server</a>
                </div>

            </div>

            <?php if (!$servers): ?>
                <p class="text-muted mb-0">Noch keine COM-Server angelegt.</p>
            <?php else: ?>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Host:Port</th>
                                <th>Status</th>
                                <th>Controller</th>
                                <th class="text-end">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servers as $server): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars((string) $server['name']) ?></td>
                                    <td>
                                        <?= htmlspecialchars((string) $server['host']) ?>:<?= (int) $server['tcp_port'] ?>
                                    </td>
                                    <td>
                                        <?php if ((int) $server['enabled'] === 1): ?>
                                            <span class="text-success">aktiv</span>
                                        <?php else: ?>
                                            <span class="text-muted">inaktiv</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int) $server['controller_count'] ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">

                                            <a
                                                class="btn btn-sm btn-outline-secondary"
                                                href="/?page=com-servers&action=edit&id=<?= (int) $server['id'] ?>"
                                            >
                                                Bearbeiten
                                            </a>

                                            <form
                                                method="post"
                                                onsubmit="return confirm(<?= htmlspecialchars(
                                                    json_encode(
                                                        'COM-Server „' . (string) $server['name'] . '“ wirklich löschen?',
                                                        JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>);"
                                            >
                                                <input type="hidden" name="page" value="com-servers">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int) $server['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Löschen</button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</main>

</body>
</html>
