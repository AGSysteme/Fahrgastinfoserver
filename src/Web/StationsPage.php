<?php

declare(strict_types=1);

namespace Ela\Web;

use RuntimeException;

final class StationsPage extends Page
{
    public function post(string $action): void
    {
        $stations = $this->services->stations;

        if ($action === 'create') {
            $name = $this->text($_POST, 'name');

            if ($name === '') {
                throw new RuntimeException('Der Name der Haltestelle ist erforderlich.');
            }

            $stations->create(
                $name,
                $this->text($_POST, 'short_name'),
                $this->text($_POST, 'station_code'),
                $this->optionalFloat($_POST, 'latitude'),
                $this->optionalFloat($_POST, 'longitude')
            );

            $this->redirect('/?page=stations');
        }

        if ($action === 'update') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0) {
                throw new RuntimeException('Ungültige Haltestelle.');
            }

            $name = $this->text($_POST, 'name');

            if ($name === '') {
                throw new RuntimeException('Der Name der Haltestelle ist erforderlich.');
            }

            $stations->update(
                $id,
                $name,
                $this->text($_POST, 'short_name'),
                $this->text($_POST, 'station_code'),
                $this->optionalFloat($_POST, 'latitude'),
                $this->optionalFloat($_POST, 'longitude'),
                isset($_POST['active'])
            );

            $this->redirect('/?page=stations');
        }

        if ($action === 'delete') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0) {
                throw new RuntimeException('Ungültige Haltestelle.');
            }

            $stations->delete($id);

            $this->redirect('/?page=stations');
        }
    }

    public function get(string $action): void
    {
        $stations = $this->services->stations;

        if ($action === 'new') {
            $this->render('station-form.php', ['station' => null]);
        }

        if ($action === 'edit') {
            $station = $stations->find($this->number($_GET, 'id'));

            if (!$station) {
                $this->abort(404, 'Haltestelle nicht gefunden.');
            }

            $this->render('station-form.php', ['station' => $station]);
        }

        $search = $this->text($_GET, 'search');

        $this->render('stations.php', [
            'stations' => $stations->all($search),
            'search' => $search,
        ]);
    }
}
