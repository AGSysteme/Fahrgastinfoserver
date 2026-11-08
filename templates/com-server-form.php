<?php
/**
 * @var array|null $comServer
 * @var string|null $error
 * @var string $csrfToken
 */

$editing = $comServer !== null && (int) ($comServer['id'] ?? 0) > 0;

$name = (string) ($comServer['name'] ?? '');
$host = (string) ($comServer['host'] ?? '');
$tcpPort = (string) ($comServer['tcp_port'] ?? 8000);
$enabled = $comServer === null
    ? true
    : (int) ($comServer['enabled'] ?? 0) === 1;
?>
<?php $pageTitle = $editing ? 'COM-Server bearbeiten' : 'Neuer COM-Server'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 720px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <div class="mb-4">
                <h2 class="h4 mb-1">
                    <?= $editing ? 'COM-Server bearbeiten' : 'Neuer COM-Server' ?>
                </h2>
                <div class="text-muted small">
                    Die Verbindung, über die Controller angesprochen werden.
                </div>
            </div>

            <form method="post" action="/?page=com-servers">

                <input type="hidden" name="page" value="com-servers">
                <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?= (int) $comServer['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= htmlspecialchars($name) ?>"
                        required
                    >
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-8">
                        <label for="host" class="form-label">Host</label>
                        <input
                            id="host"
                            type="text"
                            name="host"
                            class="form-control"
                            value="<?= htmlspecialchars($host) ?>"
                            placeholder="z. B. 192.168.1.20"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tcp_port" class="form-label">TCP-Port</label>
                        <input
                            id="tcp_port"
                            type="number"
                            name="tcp_port"
                            class="form-control"
                            value="<?= htmlspecialchars($tcpPort) ?>"
                            min="1"
                            max="65535"
                            required
                        >
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input
                        id="enabled"
                        type="checkbox"
                        name="enabled"
                        class="form-check-input"
                        <?= $enabled ? 'checked' : '' ?>
                    >
                    <label for="enabled" class="form-check-label">Aktiv</label>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a class="btn btn-secondary" href="/?page=com-servers">Abbrechen</a>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>

            </form>

        </div>
    </div>

</main>

</body>
</html>
