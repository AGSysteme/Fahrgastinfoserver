<?php

declare(strict_types=1);

namespace Ela\Web;

final class LinesPage extends Page
{
    public function post(string $action): void
    {
        $lines = $this->services->lines;

        if ($action === 'stations-save') {
            $lineId = $this->number($_POST, 'line_id');

            $lines->replaceStations($lineId, $_POST['station_ids'] ?? []);

            $this->redirect('/?page=lines&action=stations&id=' . $lineId);
        }

        if ($action === 'create') {
            $lines->create(
                $this->text($_POST, 'number'),
                $this->text($_POST, 'name'),
                $this->text($_POST, 'description')
            );

            $this->redirect('/?page=lines');
        }

        if ($action === 'update') {
            $lines->update(
                $this->number($_POST, 'id'),
                $this->text($_POST, 'number'),
                $this->text($_POST, 'name'),
                $this->text($_POST, 'description'),
                isset($_POST['active'])
            );

            $this->redirect('/?page=lines');
        }

        if ($action === 'delete') {
            $lines->delete($this->number($_POST, 'id'));

            $this->redirect('/?page=lines');
        }
    }

    public function get(string $action): void
    {
        $lines = $this->services->lines;

        if ($action === 'stations') {
            $line = $lines->find($this->number($_GET, 'id'));

            if (!$line) {
                $this->abort(404, 'Linie nicht gefunden.');
            }

            $this->render('line-stations.php', [
                'line' => $line,
                'stations' => $lines->stations((int) $line['id']),
                'allStations' => $this->services->stations->all(),
            ]);
        }

        if ($action === 'new') {
            $this->render('line-form.php', ['line' => null]);
        }

        if ($action === 'edit') {
            $line = $lines->find($this->number($_GET, 'id'));

            if (!$line) {
                $this->abort(404, 'Linie nicht gefunden.');
            }

            $this->render('line-form.php', ['line' => $line]);
        }

        $search = $this->text($_GET, 'search');

        $this->render('lines.php', [
            'lines' => $lines->all($search),
            'search' => $search,
        ]);
    }
}
