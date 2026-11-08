
<?php
/**
 * @var array|null $controller
 * @var array $comServers
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<?php $pageTitle = $controller ? 'Controller bearbeiten' : 'Neuer Controller'; require __DIR__ . '/partials/layout-start.php'; ?>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1240px;">

    <?php require __DIR__ . '/partials/alert.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Überschrift -->
            <div class="mb-4">
                <h2 class="h4 mb-1">
                    <?= $controller ? 'Controller bearbeiten' : 'Neuer Controller' ?>
                </h2>

                <div class="text-muted small">
                    <?= $controller
                        ? 'Technische Einstellungen des Controllers bearbeiten.'
                        : 'Einen neuen ELA-Controller konfigurieren.' ?>
                </div>
            </div>


            <form method="post">

                <input type="hidden" name="page" value="controllers">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken) ?>"
                >

                <input
                    type="hidden"
                    name="action"
                    value="<?= $controller ? 'update' : 'create' ?>"
                >

                <?php if ($controller): ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $controller['id'] ?>"
                    >
                <?php endif; ?>

                <!-- Allgemein -->
                <div class="card mb-4">
                    <div class="card-body p-3 p-md-4">

                        <h3 class="h5 mb-1">Allgemein</h3>

                        <p class="text-muted small mb-4">
                            Grundlegende Identifikation des Controllers.
                        </p>

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">
                                    Name <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    required
                                    maxlength="100"
                                    autocomplete="off"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['name'] ?? '')
                                    ) ?>"
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="device_id" class="form-label">
                                    Geräte-ID <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="device_id"
                                    type="text"
                                    name="device_id"
                                    class="form-control"
                                    required
                                    maxlength="100"
                                    autocomplete="off"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['device_id'] ?? '')
                                    ) ?>"
                                >

                                <div class="form-text">
                                    Eindeutige Kennung des physischen Geräts.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- MQTT -->
                <div class="card mb-4">
                    <div class="card-body p-3 p-md-4">

                        <h3 class="h5 mb-1">MQTT</h3>

                        <p class="text-muted small mb-4">
                            Verbindungseinstellungen für die Kommunikation mit dem ELA-System.
                        </p>

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="mqtt_client_id" class="form-label">
                                    MQTT Client-ID
                                </label>

                                <input
                                    id="mqtt_client_id"
                                    type="text"
                                    name="mqtt_client_id"
                                    class="form-control"
                                    maxlength="200"
                                    autocomplete="off"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['mqtt_client_id'] ?? '')
                                    ) ?>"
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="mqtt_topic" class="form-label">
                                    MQTT Topic
                                </label>

                                <input
                                    id="mqtt_topic"
                                    type="text"
                                    name="mqtt_topic"
                                    class="form-control"
                                    maxlength="200"
                                    autocomplete="off"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['mqtt_topic'] ?? '')
                                    ) ?>"
                                >
                            </div>

                            <div class="col-12">
                                <label for="ip_address" class="form-label">
                                    IP-Adresse
                                </label>

                                <input
                                    id="ip_address"
                                    type="text"
                                    name="ip_address"
                                    class="form-control"
                                    maxlength="45"
                                    autocomplete="off"
                                    placeholder="z. B. 192.168.1.50"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['ip_address'] ?? '')
                                    ) ?>"
                                >

                                <div class="form-text">
                                    Optional. Kann leer gelassen werden.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- COM-Server -->
                <div class="card mb-4">
                    <div class="card-body p-3 p-md-4">

                        <h3 class="h5 mb-1">COM-Server</h3>

                        <p class="text-muted small mb-4">
                            Zuordnung des Controllers zum zuständigen ELA-COM-Server
                            und zur RS485-Schnittstelle.
                        </p>

                        <div class="row g-3">

                            <div class="col-12">
                                <label for="com_server_id" class="form-label">
                                    COM-Server
                                </label>

                                <select
                                    id="com_server_id"
                                    name="com_server_id"
                                    class="form-select"
                                >
                                    <option value="">
                                        Kein COM-Server zugeordnet
                                    </option>

                                    <?php foreach ($comServers as $comServer): ?>
                                        <option
                                            value="<?= (int) $comServer['id'] ?>"
                                            <?= (
                                                isset($controller['com_server_id']) &&
                                                (int) $controller['com_server_id'] ===
                                                (int) $comServer['id']
                                            ) ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars(
                                                (string) $comServer['name']
                                            ) ?>
                                            –
                                            <?= htmlspecialchars(
                                                (string) $comServer['host']
                                            ) ?>:<?= (int) $comServer['tcp_port'] ?>
                                            <?php if ((int) $comServer['enabled'] !== 1): ?>
                                                (deaktiviert)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>

                                <div class="form-text">
                                    Der Controller kann später über diesen COM-Server
                                    angesprochen werden.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="serial_port" class="form-label">
                                    Serieller Port
                                </label>

                                <input
                                    id="serial_port"
                                    type="number"
                                    name="serial_port"
                                    class="form-control"
                                    min="1"
                                    step="1"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['serial_port'] ?? '')
                                    ) ?>"
                                >

                                <div class="form-text">
                                    RS485-/Serieller Port am COM-Server.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="rs485_address" class="form-label">
                                    RS485-Adresse
                                </label>

                                <input
                                    id="rs485_address"
                                    type="number"
                                    name="rs485_address"
                                    class="form-control"
                                    min="1"
                                    max="247"
                                    step="1"
                                    value="<?= htmlspecialchars(
                                        (string) ($controller['rs485_address'] ?? '')
                                    ) ?>"
                                >

                                <div class="form-text">
                                    Modbus-/RS485-Adresse des Controllers.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Status nur beim Bearbeiten -->
                <?php if ($controller): ?>

                    <div class="card mb-4">
                        <div class="card-body p-3 p-md-4">

                            <h3 class="h5 mb-1">Status</h3>

                            <p class="text-muted small mb-3">
                                Deaktivierte Controller bleiben im System erhalten,
                                werden aber als inaktiv angezeigt.
                            </p>

                            <div class="form-check form-switch">
                                <input
                                    id="enabled"
                                    type="checkbox"
                                    name="enabled"
                                    value="1"
                                    class="form-check-input"
                                    role="switch"
                                    <?= (int) $controller['enabled'] === 1
                                        ? 'checked'
                                        : '' ?>
                                >

                                <label for="enabled" class="form-check-label">
                                    Controller ist aktiv
                                </label>
                            </div>

                        </div>
                    </div>

                <?php endif; ?>

                <!-- Aktionen -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

                    <a
                        href="/?page=controllers"
                        class="btn btn-secondary"
                    >
                        &larr; Abbrechen
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <?= $controller
                            ? 'Änderungen speichern'
                            : 'Controller anlegen' ?>
                    </button>

                </div>

            </form>

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
