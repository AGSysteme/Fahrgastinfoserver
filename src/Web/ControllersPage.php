<?php

declare(strict_types=1);

namespace Ela\Web;

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

            $controllers->replaceStations($controllerId, $stationIds);

            $this->redirect(
                '/?page=controllers&action=stations&id=' . $controllerId
            );
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

            $ipAddress = $this->text($_POST, 'ip_address');

            $controllers->create(
                $name,
                $deviceId,
                $this->text($_POST, 'mqtt_client_id'),
                $this->text($_POST, 'mqtt_topic'),
                $ipAddress !== '' ? $ipAddress : null,
                $this->optionalComServerId(),
                $this->optionalInt($this->text($_POST, 'serial_port')),
                $this->optionalInt($this->text($_POST, 'rs485_address'))
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

            $ipAddress = $this->text($_POST, 'ip_address');

            $controllers->update(
                $id,
                $name,
                $deviceId,
                $this->text($_POST, 'mqtt_client_id'),
                $this->text($_POST, 'mqtt_topic'),
                $ipAddress !== '' ? $ipAddress : null,
                isset($_POST['enabled']),
                $this->optionalComServerId(),
                $this->optionalInt($this->text($_POST, 'serial_port')),
                $this->optionalInt($this->text($_POST, 'rs485_address'))
            );

            $this->redirect('/?page=controllers');
        }
    }

    public function get(string $action): void
    {
        $controllers = $this->services->controllers;

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

    private function optionalComServerId(): ?int
    {
        $id = $this->number($_POST, 'com_server_id');

        return $id > 0 ? $id : null;
    }
}
