<?php

declare(strict_types=1);

namespace Ela\Web;

use RuntimeException;

final class MeasuresPage extends Page
{
    public function post(string $action): void
    {
        if ($action === 'measure-delete') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0) {
                throw new RuntimeException('Ungültige Maßnahme.');
            }

            $this->services->measures->delete($id);

            $this->redirect('/?page=measures');
        }
    }

    public function get(string $action): void
    {
        $this->render('measures.php', [
            'measures' => $this->services->measures->all(),
        ]);
    }
}
