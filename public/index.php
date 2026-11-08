<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use Ela\Audio;
use Ela\ComServers;
use Ela\Controllers;
use Ela\Csrf;
use Ela\Database;
use Ela\Lines;
use Ela\Measures;
use Ela\Router;
use Ela\Stations;
use Ela\Web\AudioPage;
use Ela\Web\ComServersPage;
use Ela\Web\ControllersPage;
use Ela\Web\DashboardPage;
use Ela\Web\LinesPage;
use Ela\Web\MeasureEditPage;
use Ela\Web\MeasuresPage;
use Ela\Web\MeasureWizardPage;
use Ela\Web\Services;
use Ela\Web\StationsPage;

$db = Database::connection();

$services = new Services(
    new Stations($db),
    new Lines($db),
    new Controllers($db),
    new Audio($db),
    new Measures($db),
    new ComServers($db)
);

$router = new Router();
$page = $router->page();
$action = $router->action();

// Alte Wizard-Daten im früheren Format verwerfen.
foreach (($_SESSION['measure_wizard']['targets'] ?? []) as $target) {
    if (is_array($target)) {
        unset($_SESSION['measure_wizard']);
        break;
    }
}

$pages = [
    'dashboard' => new DashboardPage($services),
    'stations' => new StationsPage($services),
    'lines' => new LinesPage($services),
    'controllers' => new ControllersPage($services),
    'com-servers' => new ComServersPage($services),
    'audio' => new AudioPage($services),
    'measures' => new MeasuresPage($services),
    'measure-wizard' => new MeasureWizardPage($services),
    'measure-edit' => new MeasureEditPage($services),
];

// Unbekannte Seiten zeigen das Dashboard.
$handler = $pages[$page] ?? $pages['dashboard'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Einmal für alle POST-Requests prüfen.
    if (!Csrf::verify(
        isset($_POST['csrf_token'])
            ? (string) $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('Ungültiger CSRF-Token.');
    }

    try {
        $handler->post($action);
    } catch (Throwable $e) {
        $handler->setError($e->getMessage());
    }
}

$handler->get($action);
