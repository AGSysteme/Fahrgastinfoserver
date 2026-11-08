<?php

declare(strict_types=1);

namespace Ela\Web;

use RuntimeException;

/**
 * Verwaltung der COM-Server. Controller werden später über
 * die Controller-Seite einem COM-Server zugeordnet.
 */
final class ComServersPage extends Page
{
    public function post(string $action): void
    {
        $repository = $this->services->comServers;

        if ($action === 'create' || $action === 'update') {
            $id = $this->number($_POST, 'id');
            $values = $this->formValues($id);

            if ($values['name'] === '' || $values['host'] === '') {
                throw new RuntimeException('Bitte Name und Host angeben.');
            }

            if ($values['tcp_port'] < 1 || $values['tcp_port'] > 65535) {
                throw new RuntimeException('Der TCP-Port muss zwischen 1 und 65535 liegen.');
            }

            if ($action === 'create') {
                $repository->create(
                    $values['name'],
                    $values['host'],
                    $values['tcp_port'],
                    $values['enabled']
                );
            } else {
                if ($id <= 0 || !$repository->find($id)) {
                    throw new RuntimeException('COM-Server nicht gefunden.');
                }

                $repository->update(
                    $id,
                    $values['name'],
                    $values['host'],
                    $values['tcp_port'],
                    $values['enabled']
                );
            }

            $this->redirect('/?page=com-servers');
        }

        if ($action === 'delete') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0 || !$repository->find($id)) {
                throw new RuntimeException('COM-Server nicht gefunden.');
            }

            if ($repository->usageCount($id) > 0) {
                throw new RuntimeException(
                    'Der COM-Server ist noch Controllern zugeordnet und kann nicht gelöscht werden.'
                );
            }

            $repository->delete($id);

            $this->redirect('/?page=com-servers');
        }
    }

    public function get(string $action): void
    {
        $repository = $this->services->comServers;

        // Neuer Server oder Formular nach fehlgeschlagener Speicherung
        if ($action === 'new' || $action === 'create') {
            $this->render('com-server-form.php', [
                'comServer' => $action === 'create' ? $this->formValues(0) : null,
            ]);
        }

        // Bearbeiten oder Formular nach fehlgeschlagener Änderung
        if ($action === 'edit' || $action === 'update') {
            $id = $action === 'edit'
                ? $this->number($_GET, 'id')
                : $this->number($_POST, 'id');

            if ($action === 'edit') {
                $server = $repository->find($id);

                if (!$server) {
                    $this->abort(404, 'COM-Server nicht gefunden.');
                }
            } else {
                $server = $this->formValues($id);
            }

            $this->render('com-server-form.php', [
                'comServer' => $server,
            ]);
        }

        $this->render('com-servers.php', [
            'servers' => $repository->all(),
        ]);
    }

    /**
     * Formularwerte aus dem POST-Request, auch für die erneute Anzeige nach einem Fehler.
     *
     * @return array{id: int, name: string, host: string, tcp_port: int, enabled: bool}
     */
    private function formValues(int $id): array
    {
        $port = $this->text($_POST, 'tcp_port');

        return [
            'id' => $id,
            'name' => $this->text($_POST, 'name'),
            'host' => $this->text($_POST, 'host'),
            'tcp_port' => $port !== '' ? (int) $port : 8000,
            'enabled' => isset($_POST['enabled']),
        ];
    }
}
