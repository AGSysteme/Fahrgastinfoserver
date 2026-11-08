<?php

declare(strict_types=1);

namespace Ela\Web;

final class DashboardPage extends Page
{
    public function get(string $action): void
    {
        $services = $this->services;
        $measures = $services->measures->statusCounts(time());

        $this->render('dashboard.php', [
            'stationsCount' => $services->stations->count(),
            'linesCount' => $services->lines->count(),
            'controllersCount' => $services->controllers->count(),
            'audioFilesCount' => $services->audio->count(),
            'measuresCount' => $measures['total'],
            'measuresActiveCount' => $measures['active'],
            'measuresPlannedCount' => $measures['planned'],
            'measuresExpiredCount' => $measures['expired'],
        ]);
    }
}
