<?php

declare(strict_types=1);

namespace Ela\Web;

use Ela\Config;
use Ela\DeviceCommands;
use RuntimeException;

final class ControllersPage extends Page
{
    public function post(string $action): void
    {
        $controllers = $this->services->controllers;

        if ($action === 'stations-save') {
            $controllerId = $this->number($_POST, 'controller_id');

            if ($controllerId <= 0) {
                throw new RuntimeException('Ungültiger Controller.');
            }

            if (!$controllers->find($controllerId)) {
                throw new RuntimeException('Controller nicht gefunden.');
            }

            $stationIds = $_POST['stations'] ?? [];

            if (!is_array($stationIds)) {
                $stationIds = [];
            }

            $stationIds = array_values(array_unique(array_filter(
                array_map('intval', $stationIds),
                static fn(int $id): bool => $id > 0
            )));

            $controllers->replaceStations(
                $controllerId,
                $stationIds,
                $this->pinsFor($stationIds)
            );

            $this->redirect(
                '/?page=controllers&action=stations&id=' . $controllerId
            );
        }

        if ($action === 'test-play' || $action === 'stop') {
            $controllerId = $this->number($_POST, 'controller_id');

            if ($controllerId <= 0 || !$controllers->find($controllerId)) {
                throw new RuntimeException('Controller nicht gefunden.');
            }

            $commands = $this->services->deviceCommands;

            if ($action === 'stop') {
                $commands->queueStop($controllerId);
            } else {
                // Testdurchsage: nur Audio, ohne Ausgänge
                $durationMs = DeviceCommands::wavDurationMs(
                    Config::systemAudioDir() . '/testdurchsage.wav'
                );

                $commands->queuePlay($controllerId, 0, [], $durationMs);
            }

            $this->redirect('/?page=controllers&action=commands&id=' . $controllerId);
        }

        if ($action === 'delete') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0) {
                throw new RuntimeException('Ungültiger Controller.');
            }

            if (!$controllers->find($id)) {
                throw new RuntimeException('Controller nicht gefunden.');
            }

            $controllers->delete($id);

            $this->redirect('/?page=controllers');
        }

        if ($action === 'create') {
            $name = $this->text($_POST, 'name');
            $deviceId = $this->text($_POST, 'device_id');

            if ($name === '' || $deviceId === '') {
                throw new RuntimeException('Bitte alle Pflichtfelder ausfüllen.');
            }

            $c = $this->connection();

            $controllers->create(
                $name,
                $deviceId,
                $c['mqttClientId'],
                $c['mqttTopic'],
                $c['ip'],
                $c['comServerId'],
                $c['serialPort'],
                $c['rs485Address'],
                $c['transport']
            );

            $this->redirect('/?page=controllers');
        }

        if ($action === 'update') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0) {
                throw new RuntimeException('Ungültiger Controller.');
            }

            $name = $this->text($_POST, 'name');
            $deviceId = $this->text($_POST, 'device_id');

            if ($name === '' || $deviceId === '') {
                throw new RuntimeException('Bitte alle Pflichtfelder ausfüllen.');
            }

            $c = $this->connection();

            $controllers->update(
                $id,
                $name,
                $deviceId,
                $c['mqttClientId'],
                $c['mqttTopic'],
                $c['ip'],
                isset($_POST['enabled']),
                $c['comServerId'],
                $c['serialPort'],
                $c['rs485Address'],
                $c['transport']
            );

            $this->redirect('/?page=controllers');
        }
    }

    public function get(string $action): void
    {
        $controllers = $this->services->controllers;

        if ($action === 'commands') {
            $controller = $controllers->find($this->number($_GET, 'id'));

            if (!$controller) {
                $this->abort(404, 'Controller nicht gefunden.');
            }

            $this->render('controller-commands.php', [
                'controller' => $controller,
                'commands' => $this->services->deviceCommands->recent((int) $controller['id']),
            ]);
        }

        if ($action === 'stations') {
            $controller = $controllers->find($this->number($_GET, 'id'));

            if (!$controller) {
                $this->abort(404, 'Controller nicht gefunden.');
            }

            $this->render('controller-stations.php', [
                'controller' => $controller,
                'stations' => $controllers->allStations(),
            ]);
        }

        if ($action === 'new') {
            $this->render('controller-form.php', [
                'controller' => null,
                'comServers' => $controllers->comServers(),
            ]);
        }

        if ($action === 'edit') {
            $controller = $controllers->find($this->number($_GET, 'id'));

            if (!$controller) {
                $this->abort(404, 'Controller nicht gefunden.');
            }

            $this->render('controller-form.php', [
                'controller' => $controller,
                'comServers' => $controllers->comServers(),
            ]);
        }

        $search = $this->text($_GET, 'search');

        $this->render('controllers.php', [
            'controllers' => $controllers->all($search),
            'search' => $search,
        ]);
    }

    /**
     * Liest die Ausgänge (1–16) der gewählten Haltestellen. Jeder Ausgang
     * darf je Controller nur einmal vorkommen. Leer = kein Ausgang.
     *
     * @param list<int> $stationIds
     * @return array<int, int|null>
     */
    private function pinsFor(array $stationIds): array
    {
        $submitted = is_array($_POST['pins'] ?? null) ? $_POST['pins'] : [];
        $pins = [];
        $used = [];

        foreach ($stationIds as $stationId) {
            $raw = trim((string) ($submitted[$stationId] ?? ''));

            if ($raw === '') {
                $pins[$stationId] = null;
                continue;
            }

            if (!ctype_digit($raw) || (int) $raw < 1 || (int) $raw > 16) {
                throw new RuntimeException('Ausgänge müssen zwischen 1 und 16 liegen.');
            }

            $pin = (int) $raw;

            if (isset($used[$pin])) {
                throw new RuntimeException('Ausgang ' . $pin . ' ist mehrfach vergeben.');
            }

            $used[$pin] = true;
            $pins[$stationId] = $pin;
        }

        return $pins;
    }

    /**
     * Prüft die Anbindung (LAN oder RS485). Die Felder der anderen Anbindung
     * werden verworfen, damit keine widersprüchlichen Daten gespeichert werden.
     *
     * @return array{transport: string, ip: ?string, mqttClientId: string, mqttTopic: string, comServerId: ?int, serialPort: ?int, rs485Address: ?int}
     */
    private function connection(): array
    {
        $transport = $this->text($_POST, 'transport');

        if (!in_array($transport, ['lan', 'rs485'], true)) {
            throw new RuntimeException('Bitte die Anbindung wählen (LAN oder RS485).');
        }

        if ($transport === 'lan') {
            $mqttClientId = $this->text($_POST, 'mqtt_client_id');
            $mqttTopic = $this->text($_POST, 'mqtt_topic');
            $ip = $this->text($_POST, 'ip_address');

            if ($mqttClientId === '' || $mqttTopic === '') {
                throw new RuntimeException('Für LAN-Controller sind MQTT Client-ID und MQTT Topic erforderlich.');
            }

            return [
                'transport' => 'lan',
                'ip' => $ip !== '' ? $ip : null,
                'mqttClientId' => $mqttClientId,
                'mqttTopic' => $mqttTopic,
                'comServerId' => null,
                'serialPort' => null,
                'rs485Address' => null,
            ];
        }

        $comServerId = $this->optionalComServerId();
        $serialPort = $this->optionalInt($this->text($_POST, 'serial_port'));
        $rs485Address = $this->optionalInt($this->text($_POST, 'rs485_address'));

        if ($comServerId === null || $serialPort === null || $rs485Address === null) {
            throw new RuntimeException('Für RS485-Controller sind COM-Server, serieller Port und RS485-Adresse erforderlich.');
        }

        if ($serialPort < 1) {
            throw new RuntimeException('Der serielle Port muss mindestens 1 sein.');
        }

        if ($rs485Address < 1 || $rs485Address > 247) {
            throw new RuntimeException('Die RS485-Adresse muss zwischen 1 und 247 liegen.');
        }

        return [
            'transport' => 'rs485',
            'ip' => null,
            'mqttClientId' => '',
            'mqttTopic' => '',
            'comServerId' => $comServerId,
            'serialPort' => $serialPort,
            'rs485Address' => $rs485Address,
        ];
    }

    private function optionalComServerId(): ?int
    {
        $id = $this->number($_POST, 'com_server_id');

        return $id > 0 ? $id : null;
    }
}
