<?php
/**
 * @var array $lines
 * @var string $csrfToken
 * @var string|null $error
 */
?>

<?php $pageTitle = 'Linien'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1240px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">

        <div class="card-body p-3 p-md-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <h2 class="h4 mb-0">Linien</h2>

                <a
                    class="btn btn-primary"
                    href="/?page=lines&action=new"
                >
                    <span class="me-1">+</span> Neue Linie
                </a>

            </div>


            <?php if (!$lines): ?>

                <div class="alert alert-secondary mb-0">
                    Es sind noch keine Linien vorhanden.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Nummer</th>
                                <th>Name</th>
                                <th>Beschreibung</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($lines as $line): ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            (string) $line['number']
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        (string) ($line['name'] ?? '')
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        (string) ($line['description'] ?? '')
                                    ) ?>
                                </td>

                                <td>

                                    <?php if ((int) $line['active'] === 1): ?>

                                        <span class="badge text-bg-success">
                                            Aktiv
                                        </span>

                                    <?php else: ?>

                                        <span class="badge text-bg-danger">
                                            Inaktiv
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="d-flex flex-wrap gap-2">

                                        <a
                                            class="btn btn-sm btn-primary"
                                            href="/?page=lines&action=edit&id=<?= (int) $line['id'] ?>"
                                        >
                                            Bearbeiten
                                        </a>

                                        <a
                                            class="btn btn-sm btn-secondary"
                                            href="/?page=lines&action=stations&id=<?= (int) $line['id'] ?>"
                                        >
                                            Haltestellen
                                        </a>

                                        <form
                                            method="post"
                                            onsubmit="return confirm('Diese Linie wirklich löschen?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="page"
                                                value="lines"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $line['id'] ?>"
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

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

            <div class="mt-4">
                <a href="/" class="text-decoration-none">
                    &larr; Zum Dashboard
                </a>
            </div>

        </div>

    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
