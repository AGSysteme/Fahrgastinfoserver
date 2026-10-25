<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');

session_start();

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Stations.php';
require_once __DIR__ . '/../src/Csrf.php';
require_once __DIR__ . '/../src/Controllers.php';
require_once __DIR__ . '/../src/Lines.php';
require_once __DIR__ . '/../src/Audio.php';
require_once __DIR__ . '/../src/Measures.php';

use Ela\Database;
use Ela\Router;
use Ela\Stations;
use Ela\Csrf;
use Ela\Controllers;
use Ela\Lines;
use Ela\Audio;
use Ela\Measures;

$db = Database::connection();

$router = new Router();
$stationsRepository = new Stations($db);
$controllersRepository = new Controllers($db);
$comServers = $db->query(
    'SELECT id, name, host, tcp_port, enabled
     FROM com_servers
     ORDER BY name'
)->fetchAll();
$linesRepository = new Lines($db);
$audioRepository = new Audio($db);
$measuresRepository = new Measures($db);

$page = $router->page();
$action = $router->action();
foreach (($_SESSION['measure_wizard']['targets'] ?? []) as $target) {
    if (is_array($target)) {
        unset($_SESSION['measure_wizard']);
        break;
    }
}

$error = null;

$csrfToken = Csrf::token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!Csrf::verify(
        isset($_POST['csrf_token'])
            ? (string) $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('Ungültiger CSRF-Token.');
    }

    try {

        if ($page === 'stations') {

            if ($action === 'create') {

                $name = trim(
                    (string) ($_POST['name'] ?? '')
                );

                $shortName = trim(
                    (string) ($_POST['short_name'] ?? '')
                );

                $stationCode = trim(
                    (string) ($_POST['station_code'] ?? '')
                );

                if ($name === '') {
                    throw new RuntimeException(
                        'Der Name der Haltestelle ist erforderlich.'
                    );
                }

                $latitude = null;

                if (
                    isset($_POST['latitude']) &&
                    $_POST['latitude'] !== ''
                ) {
                    $latitude = (float) $_POST['latitude'];
                }

                $longitude = null;

                if (
                    isset($_POST['longitude']) &&
                    $_POST['longitude'] !== ''
                ) {
                    $longitude = (float) $_POST['longitude'];
                }

                $stationsRepository->create(
                    $name,
                    $shortName,
                    $stationCode,
                    $latitude,
                    $longitude
                );

                header(
                    'Location: /?page=stations'
                );

                exit;
            }

            if ($action === 'update') {

                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new RuntimeException(
                        'Ungültige Haltestelle.'
                    );
                }

                $name = trim(
                    (string) ($_POST['name'] ?? '')
                );

                $shortName = trim(
                    (string) ($_POST['short_name'] ?? '')
                );

                $stationCode = trim(
                    (string) ($_POST['station_code'] ?? '')
                );

                if ($name === '') {
                    throw new RuntimeException(
                        'Der Name der Haltestelle ist erforderlich.'
                    );
                }

                $latitude = null;

                if (
                    isset($_POST['latitude']) &&
                    $_POST['latitude'] !== ''
                ) {
                    $latitude = (float) $_POST['latitude'];
                }

                $longitude = null;

                if (
                    isset($_POST['longitude']) &&
                    $_POST['longitude'] !== ''
                ) {
                    $longitude = (float) $_POST['longitude'];
                }

                $active = isset($_POST['active']);

                $stationsRepository->update(
                    $id,
                    $name,
                    $shortName,
                    $stationCode,
                    $latitude,
                    $longitude,
                    $active
                );

                header(
                    'Location: /?page=stations'
                );

                exit;
            }

            if ($action === 'delete') {

                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new RuntimeException(
                        'Ungültige Haltestelle.'
                    );
                }

                $stationsRepository->delete($id);

                header(
                    'Location: /?page=stations'
                );

                exit;
            }
        }

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!Csrf::verify(
        isset($_POST['csrf_token'])
            ? (string) $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('Ungültiger CSRF-Token.');
    }

    try {

        if ($page === 'controllers') {

if ($action === 'stations-save') {

    $controllerId = (int) (
        $_POST['controller_id'] ?? 0
    );

    if ($controllerId <= 0) {
        throw new RuntimeException(
            'Ungültiger Controller.'
        );
    }

    $controller =
        $controllersRepository->find($controllerId);

    if (!$controller) {
        throw new RuntimeException(
            'Controller nicht gefunden.'
        );
    }

    $stationIds = $_POST['stations'] ?? [];

    if (!is_array($stationIds)) {
        $stationIds = [];
    }

    $stationIds = array_map(
        'intval',
        $stationIds
    );

    $stationIds = array_values(
        array_unique(
            array_filter(
                $stationIds,
                static fn(int $id): bool => $id > 0
            )
        )
    );

        $controllersRepository->replaceStations(
        $controllerId,
        $stationIds
    );

    header(
        'Location: /?page=controllers&action=stations&id=' .
        $controllerId
    );

    exit;
}

if ($action === 'delete') {

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        throw new RuntimeException(
            'Ungültiger Controller.'
        );
    }

    $controller =
        $controllersRepository->find($id);

    if (!$controller) {
        throw new RuntimeException(
            'Controller nicht gefunden.'
        );
    }

    $controllersRepository->delete($id);

    header(
        'Location: /?page=controllers'
    );

    exit;
}

if ($action === 'create') {
                $name = trim(
                    (string) ($_POST['name'] ?? '')
                );

                $deviceId = trim(
                    (string) ($_POST['device_id'] ?? '')
                );

                $mqttClientId = trim(
                    (string) (
                        $_POST['mqtt_client_id'] ?? ''
                    )
                );

                $mqttTopic = trim(
                    (string) (
                        $_POST['mqtt_topic'] ?? ''
                    )
                );

                $ipAddress = trim(
                    (string) (
                        $_POST['ip_address'] ?? ''
                    )
                );

$comServerId = (int) (
    $_POST['com_server_id'] ?? 0
);

$serialPort = trim(
    (string) (
        $_POST['serial_port'] ?? ''
    )
);

$rs485Address = trim(
    (string) (
        $_POST['rs485_address'] ?? ''
    )
);

                if (
                    $name === '' ||
                    $deviceId === '' 
                ) {
                    throw new RuntimeException(
                        'Bitte alle Pflichtfelder ausfüllen.'
                    );
                }

                $controllersRepository->create(
    $name,
    $deviceId,
    $mqttClientId,
    $mqttTopic,
    $ipAddress !== ''
        ? $ipAddress
        : null,
    $comServerId > 0
        ? $comServerId
        : null,
    $serialPort !== ''
        ? (int) $serialPort
        : null,
    $rs485Address !== ''
        ? (int) $rs485Address
        : null
);
                header(
                    'Location: /?page=controllers'
                );

                exit;
            }

            if ($action === 'update') {

                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new RuntimeException(
                        'Ungültiger Controller.'
                    );
                }

                $name = trim(
                    (string) ($_POST['name'] ?? '')
                );

                $deviceId = trim(
                    (string) ($_POST['device_id'] ?? '')
                );

                $mqttClientId = trim(
                    (string) (
                        $_POST['mqtt_client_id'] ?? ''
                    )
                );

                $mqttTopic = trim(
                    (string) (
                        $_POST['mqtt_topic'] ?? ''
                    )
                );

                $ipAddress = trim(
                    (string) (
                        $_POST['ip_address'] ?? ''
                    )
                );

$comServerId = (int) (
    $_POST['com_server_id'] ?? 0
);

$serialPort = trim(
    (string) (
        $_POST['serial_port'] ?? ''
    )
);

$rs485Address = trim(
    (string) (
        $_POST['rs485_address'] ?? ''
    )
);

                if (
                    $name === '' ||
                    $deviceId === '' 
                ) {
                    throw new RuntimeException(
                        'Bitte alle Pflichtfelder ausfüllen.'
                    );
                }

$controllersRepository->update(
    $id,
    $name,
    $deviceId,
    $mqttClientId,
    $mqttTopic,
    $ipAddress !== ''
        ? $ipAddress
        : null,
    isset($_POST['enabled']),
    $comServerId > 0
        ? $comServerId
        : null,
    $serialPort !== ''
        ? (int) $serialPort
        : null,
    $rs485Address !== ''
        ? (int) $rs485Address
        : null
);

                header(
                    'Location: /?page=controllers'
                );

                exit;
            }
        }

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $page === 'measures' &&
    $action === 'measure-delete'
) {

    try {

        $id =
            (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Maßnahme.'
            );
        }

        $measuresRepository->delete($id);

        header(
            'Location: /?page=measures'
        );

        exit;

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $page === 'measure-wizard'
) {

    if (!Csrf::verify(
        isset($_POST['csrf_token'])
            ? (string) $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('Ungültiger CSRF-Token.');
    }

    try {

        if ($action === 'measure-wizard-step-1') {

            $name = trim(
                (string) ($_POST['name'] ?? '')
            );

            $audioId = (int) (
                $_POST['audio_id'] ?? 0
            );

            if ($name === '') {
                throw new RuntimeException(
                    'Der Name der Maßnahme ist erforderlich.'
                );
            }

            if ($audioId <= 0) {
                throw new RuntimeException(
                    'Bitte eine Ansage auswählen.'
                );
            }

            $audio =
                $audioRepository->find($audioId);

            if (!$audio) {
                throw new RuntimeException(
                    'Die ausgewählte Ansage wurde nicht gefunden.'
                );
            }

$editId = 0;
$existingTargets = [];
$existingActive = true;

if (
    isset($_SESSION['measure_wizard']) &&
    is_array($_SESSION['measure_wizard'])
) {
    $editId =
        (int) (
            $_SESSION['measure_wizard']['edit_id']
            ?? 0
        );

    if (
        isset($_SESSION['measure_wizard']['targets']) &&
        is_array($_SESSION['measure_wizard']['targets'])
    ) {
        $existingTargets =
            $_SESSION['measure_wizard']['targets'];
    }

    if (
        array_key_exists(
            'active',
            $_SESSION['measure_wizard']
        )
    ) {
        $existingActive =
            (bool) $_SESSION['measure_wizard']['active'];
    }
}

$existingSchedule = [];

if (
    isset($_SESSION['measure_wizard']['schedule']) &&
    is_array($_SESSION['measure_wizard']['schedule'])
) {
    $existingSchedule =
        $_SESSION['measure_wizard']['schedule'];
}

$_SESSION['measure_wizard'] = [
    'name' =>
        $name,

    'audio_id' =>
        $audioId,

    'active' =>
        $existingActive,

    'targets' =>
        $existingTargets,

    'schedule' =>
        $existingSchedule
];

if ($editId > 0) {

    $_SESSION['measure_wizard']['edit_id'] =
        $editId;
}

            header(
                'Location: /?page=measure-wizard&step=2'
            );

            exit;
        }

        if ($action === 'measure-wizard-step-2') {
            $targets = $measuresRepository->validateTargets($_POST['stations'] ?? []);

            if (
                !isset($_SESSION['measure_wizard']) ||
                !is_array($_SESSION['measure_wizard']) ||
                empty($_SESSION['measure_wizard']['name']) ||
                empty($_SESSION['measure_wizard']['audio_id'])
            ) {
                header(
                    'Location: /?page=measure-wizard&step=1'
                );

                exit;
            }

            $_SESSION['measure_wizard']['targets'] =
                $targets;

            header(
                'Location: /?page=measure-wizard&step=3'
            );

            exit;
        }

if ($action === 'measure-wizard-step-3') {

    $scheduleType =
        (string) ($_POST['schedule_type'] ?? '');

    if (!in_array(
        $scheduleType,
        ['once', 'period'],
        true
    )) {
        throw new RuntimeException(
            'Ungültige Wiedergabeart.'
        );
    }

    if (
        !isset($_SESSION['measure_wizard']) ||
        !is_array($_SESSION['measure_wizard']) ||
        empty($_SESSION['measure_wizard']['name']) ||
        empty($_SESSION['measure_wizard']['audio_id']) ||
        empty($_SESSION['measure_wizard']['targets'])
    ) {
        header(
            'Location: /?page=measure-wizard&step=1'
        );

        exit;
    }

    if ($scheduleType === 'once') {

        $_SESSION['measure_wizard']['schedule'] = [
            'type' => 'once'
        ];

    }

    if ($scheduleType === 'period') {

        $startAt =
            trim(
                (string) ($_POST['start_at'] ?? '')
            );

        $endAt =
            trim(
                (string) ($_POST['end_at'] ?? '')
            );

        $interval =
            (int) (
                $_POST['interval'] ?? 0
            );

        $disableNight =
            isset($_POST['disable_night']);

        if (!in_array(
            $interval,
            [5, 10, 30, 60],
            true
        )) {
            throw new RuntimeException(
                'Ungültiges Intervall.'
            );
        }

        $startDate =
            DateTime::createFromFormat(
                'Y-m-d\TH:i',
                $startAt
            );

        $endDate =
            DateTime::createFromFormat(
                'Y-m-d\TH:i',
                $endAt
            );

        if (
            !$startDate ||
            $startDate->format('Y-m-d\TH:i')
                !== $startAt
        ) {
            throw new RuntimeException(
                'Der Beginn ist ungültig.'
            );
        }

        if (
            !$endDate ||
            $endDate->format('Y-m-d\TH:i')
                !== $endAt
        ) {
            throw new RuntimeException(
                'Das Ende ist ungültig.'
            );
        }

        if ($endDate <= $startDate) {

            throw new RuntimeException(
                'Das Ende muss nach dem Beginn liegen.'
            );
        }

        $_SESSION['measure_wizard']['schedule'] = [

            'type' =>
                'period',

            'start_at' =>
                $startAt,

            'end_at' =>
                $endAt,

            'interval' =>
                $interval,

            'disable_night' =>
                $disableNight
        ];
    }

    header(
        'Location: /?page=measure-wizard&step=4'
    );

    exit;
}

        if ($action === 'measure-wizard-finish') {

            $wizard =
                $_SESSION['measure_wizard']
                    ?? null;

            if (
                !is_array($wizard) ||
                empty($wizard['name']) ||
                empty($wizard['audio_id']) ||
                empty($wizard['targets']) ||
                empty($wizard['schedule'])
            ) {

                throw new RuntimeException(
                    'Die Daten der Maßnahme sind unvollständig.'
                );
            }

            $name =
                (string) $wizard['name'];

            $audioId =
                (int) $wizard['audio_id'];

            $targets =
                $wizard['targets'];

            $schedule =
                $wizard['schedule'];

            if (!is_array($targets)) {

                throw new RuntimeException(
                    'Die Ziele der Maßnahme sind ungültig.'
                );
            }

            if (!is_array($schedule)) {

                throw new RuntimeException(
                    'Die Zeitplanung der Maßnahme ist ungültig.'
                );
            }

            $scheduleType =
                (string) (
                    $schedule['type'] ?? ''
                );

            if ($scheduleType === 'once') {

                $startAt =
                    date('Y-m-d H:i:s');

                $endAt = null;

                $repeatType =
                    'once';

                $repeatInterval =
                    1;

                $disableNight =
                    false;
            }

            elseif ($scheduleType === 'period') {

                $startAt =
                    trim(
                        (string) (
                            $schedule['start_at'] ?? ''
                        )
                    );

                $endAt =
                    trim(
                        (string) (
                            $schedule['end_at'] ?? ''
                        )
                    );

                $repeatType =
                    'interval';

                $repeatInterval =
                    (int) (
                        $schedule['interval'] ?? 0
                    );

                $disableNight =
                    !empty(
                        $schedule['disable_night']
                    );

                if (!in_array(
                    $repeatInterval,
                    [5, 10, 30, 60],
                    true
                )) {

                    throw new RuntimeException(
                        'Ungültiges Wiederholungsintervall.'
                    );
                }

                $startDate =
                    DateTime::createFromFormat(
                        'Y-m-d\TH:i',
                        $startAt
                    );

                $endDate =
                    DateTime::createFromFormat(
                        'Y-m-d\TH:i',
                        $endAt
                    );

                if (
                    !$startDate ||
                    $startDate->format(
                        'Y-m-d\TH:i'
                    ) !== $startAt
                ) {

                    throw new RuntimeException(
                        'Der Beginn der Maßnahme ist ungültig.'
                    );
                }

                if (
                    !$endDate ||
                    $endDate->format(
                        'Y-m-d\TH:i'
                    ) !== $endAt
                ) {

                    throw new RuntimeException(
                        'Das Ende der Maßnahme ist ungültig.'
                    );
                }

                if ($endDate <= $startDate) {

                    throw new RuntimeException(
                        'Das Ende muss nach dem Beginn liegen.'
                    );
                }
            }

            else {

                throw new RuntimeException(
                    'Ungültige Zeitplanung.'
                );
            }

            $editId =
                (int) ($wizard['edit_id'] ?? 0);

            if ($editId > 0) {

                $measuresRepository->updateWithTargets(
                    $editId,
                    $name,
                    $audioId,
                    $startAt,
                    $endAt,
                    $repeatType,
                    $repeatInterval,
                    !empty($wizard['active']),
                    $disableNight,
                    $targets
                );

                $measureId = $editId;

            } else {

                $measureId =
                    $measuresRepository->createWithTargets(
                        $name,
                        $audioId,
                        $startAt,
                        $endAt,
                        $repeatType,
                        $repeatInterval,
                        $disableNight,
                        $targets
                    );
            }

            unset(
                $_SESSION['measure_wizard']
            );

            header(
                'Location: /?page=measures'
            );

            exit;
        }

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $page === 'lines'
) {

    if (!Csrf::verify(
        isset($_POST['csrf_token'])
            ? (string) $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('Ungültiger CSRF-Token.');
    }

    try {

        if ($action === 'stations-save') {
            $lineId = (int) ($_POST['line_id'] ?? 0);
            $linesRepository->replaceStations($lineId, $_POST['station_ids'] ?? []);
            header('Location: /?page=lines&action=stations&id=' . $lineId);
            exit;
        }
        if ($action === 'create') {

            $number = trim(
                (string) ($_POST['number'] ?? '')
            );

            $name = trim(
                (string) ($_POST['name'] ?? '')
            );

            $description = trim(
                (string) ($_POST['description'] ?? '')
            );

            $linesRepository->create(
                $number,
                $name,
                $description
            );

            header(
                'Location: /?page=lines'
            );

            exit;
        }

        if ($action === 'update') {

            $id = (int) ($_POST['id'] ?? 0);

            $number = trim(
                (string) ($_POST['number'] ?? '')
            );

            $name = trim(
                (string) ($_POST['name'] ?? '')
            );

            $description = trim(
                (string) ($_POST['description'] ?? '')
            );

            $linesRepository->update(
                $id,
                $number,
                $name,
                $description,
                isset($_POST['active'])
            );

            header(
                'Location: /?page=lines'
            );

            exit;
        }

        if ($action === 'delete') {

            $id = (int) ($_POST['id'] ?? 0);

            $linesRepository->delete($id);

            header(
                'Location: /?page=lines'
            );

            exit;
        }

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $page === 'audio'
) {

    if (!Csrf::verify(
        isset($_POST['csrf_token'])
            ? (string) $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('Ungültiger CSRF-Token.');
    }

    try {

        if ($action === 'generate') {

            $text = trim(
                (string) ($_POST['text'] ?? '')
            );

            $voice = trim(
                (string) ($_POST['voice'] ?? '')
            );

            if ($text === '') {
                throw new RuntimeException(
                    'Bitte einen Ansagetext eingeben.'
                );
            }

            if (!in_array(
                $voice,
                ['kerstin', 'thorsten'],
                true
            )) {
                throw new RuntimeException(
                    'Ungültige Stimme.'
                );
            }

            $generated =
                $audioRepository->generate(
                    $text,
                    $voice
                );

$audioRepository->create(
    $text,
    $text,
    $voice,
    $generated['filename'],
    $generated['format'],
    (int) $generated['sample_rate'],
    (int) $generated['channels'],
    (int) $generated['bit_depth'],
    isset($generated['duration'])
        ? (float) $generated['duration']
        : null,
    isset($generated['file_size'])
        ? (int) $generated['file_size']
        : null
);
            header(
                'Location: /?page=audio'
            );

            exit;
        }

if ($action === 'record') {

    if (
        !isset($_FILES['audio']) ||
        $_FILES['audio']['error'] !== UPLOAD_ERR_OK
    ) {
        throw new RuntimeException(
            'Keine Aufnahme hochgeladen.'
        );
    }

    $title = trim(
        $_POST['title'] ?? ''
    );

    if ($title === '') {
        throw new RuntimeException(
            'Name der Aufnahme fehlt.'
        );
    }

    $audioRepository->createFromRecording(
        $_FILES['audio'],
        $title
    );

    http_response_code(200);

    exit;
}


if ($action === 'delete') {

            $id = (int) (
                $_POST['id'] ?? 0
            );

            if ($id <= 0) {
                throw new RuntimeException(
                    'Ungültige Ansage.'
                );
            }

            $audio =
                $audioRepository->find($id);

            if (!$audio) {
                throw new RuntimeException(
                    'Ansage nicht gefunden.'
                );
            }

            $audioRepository->delete($id);

            $filename =
                basename(
                    (string) $audio['filename']
                );

            $audioFile =
                '/opt/ela/audio/announcements/' .
                $filename;

            if (is_file($audioFile)) {
                unlink($audioFile);
            }

            header(
                'Location: /?page=audio'
            );

            exit;
        }

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}

if ($page === 'lines') {

    if ($action === 'stations') {
        $line = $linesRepository->find((int) ($_GET['id'] ?? 0));
        if (!$line) { http_response_code(404); exit('Linie nicht gefunden.'); }
        $stations = $linesRepository->stations((int) $line['id']);
        $allStations = $stationsRepository->all();
        require __DIR__ . '/../templates/line-stations.php';
        exit;
    }
    if ($action === 'new') {

        $line = null;

        require __DIR__ .
            '/../templates/line-form.php';

        exit;
    }

    if ($action === 'edit') {

        $id = (int) ($_GET['id'] ?? 0);

        $line = $linesRepository->find($id);

        if (!$line) {
            http_response_code(404);
            exit('Linie nicht gefunden.');
        }

        require __DIR__ .
            '/../templates/line-form.php';

        exit;
    }

    $search = trim(
        (string) ($_GET['search'] ?? '')
    );

    $lines = $linesRepository->all($search);

    require __DIR__ .
        '/../templates/lines.php';

    exit;
}

if ($page === 'audio') {

    $audioFiles =
        $audioRepository->all();

    require __DIR__ .
        '/../templates/audio.php';

    exit;
}

if ($page === 'measures') {

    $measures =
        $measuresRepository->all();

    require __DIR__ .
        '/../templates/measures.php';

    exit;
}

if ($page === 'measure-edit') {

    $id = (int) (
        $_GET['id'] ?? 0
    );

    if ($id <= 0) {
        http_response_code(400);
        exit('Ungültige Maßnahme.');
    }

    $measure =
        $measuresRepository->find($id);

    if (!$measure) {
        http_response_code(404);
        exit('Maßnahme nicht gefunden.');
    }

    $targets =
        $measuresRepository->targets($id);

    $repeatType =
        (string) $measure['repeat_type'];

    if ($repeatType === 'once') {

        $schedule = [
            'type' =>
                'once'
        ];

    } else {

        $schedule = [
            'type' =>
                'period',

            'start_at' =>
                date(
                    'Y-m-d\TH:i',
                    strtotime(
                        (string) $measure['start_at']
                    )
                ),

            'end_at' =>
                !empty($measure['end_at'])
                    ? date(
                        'Y-m-d\TH:i',
                        strtotime(
                            (string) $measure['end_at']
                        )
                    )
                    : '',

            'interval' =>
                (int) $measure['repeat_interval'],

            'disable_night' =>
                !empty($measure['disable_night'])
        ];
    }

    $_SESSION['measure_wizard'] = [

        'edit_id' =>
            $id,

        'name' =>
            (string) $measure['name'],

        'audio_id' =>
            (int) $measure['audio_id'],

        'active' =>
            !empty($measure['active']),

        'targets' =>
            $targets,

        'schedule' =>
            $schedule
    ];

    $savedTargets =
        $targets;

    $page = 'measure-wizard';
    $_GET['step'] = '1';
}

if ($page === 'measure-wizard') {

    if (
        isset($_GET['new']) &&
        (string) $_GET['new'] === '1'
    ) {
        unset($_SESSION['measure_wizard']);
    }

    $step = (int) (
        $_GET['step'] ?? 1
    );

    if (
        $step === 1 &&
        isset($_GET['new']) &&
        $_GET['new'] === '1'
    ) {
        unset($_SESSION['measure_wizard']);
    }

    if ($step === 1) {

        $audioFiles =
            $audioRepository->all();

$wizard =
    $_SESSION['measure_wizard']
        ?? [];

$selectedAudioId =
    !empty($wizard['audio_id'])
        ? (int) $wizard['audio_id']
        : (
            isset($_GET['audio_id'])
                ? (int) $_GET['audio_id']
                : 0
        );

        require __DIR__ .
            '/../templates/measure-wizard-step-1.php';

        exit;
    }

if ($step === 2) {

    $wizard =
        $_SESSION['measure_wizard']
            ?? null;

    if (
        !is_array($wizard) ||
        empty($wizard['name']) ||
        empty($wizard['audio_id'])
    ) {

        header(
            'Location: /?page=measure-wizard&step=1'
        );

        exit;
    }

    $audio =
        $audioRepository->find(
            (int) $wizard['audio_id']
        );

    if (!$audio) {

        throw new RuntimeException(
            'Die ausgewählte Ansage wurde nicht gefunden.'
        );
    }

    $savedTargets = $wizard['targets'] ?? [];
    $stations = $stationsRepository->all();
    $wizardLines = [];
    foreach ($linesRepository->all() as $line) {
        if ((int) $line['active'] !== 1) continue;
        $line['stations'] = $linesRepository->stations((int) $line['id']);
        $wizardLines[] = $line;
    }
    require __DIR__ .
        '/../templates/measure-wizard-step-2.php';

    exit;
}

if ($step === 3) {

    $wizard =
        $_SESSION['measure_wizard']
            ?? null;

    if (
        !is_array($wizard) ||
        empty($wizard['name']) ||
        empty($wizard['audio_id']) ||
        empty($wizard['targets'])
    ) {

        header(
            'Location: /?page=measure-wizard&step=1'
        );

        exit;
    }

    $audio =
        $audioRepository->find(
            (int) $wizard['audio_id']
        );

    if (!$audio) {

        throw new RuntimeException(
            'Die ausgewählte Ansage wurde nicht gefunden.'
        );
    }

    require __DIR__ .
        '/../templates/measure-wizard-step-3.php';

    exit;
}

if ($step === 4) {

    $wizard =
        $_SESSION['measure_wizard']
            ?? null;

    if (
        !is_array($wizard) ||
        empty($wizard['name']) ||
        empty($wizard['audio_id']) ||
        empty($wizard['targets']) ||
        empty($wizard['schedule'])
    ) {

        header(
            'Location: /?page=measure-wizard&step=1'
        );

        exit;
    }

    $audio =
        $audioRepository->find(
            (int) $wizard['audio_id']
        );

    if (!$audio) {

        throw new RuntimeException(
            'Die ausgewählte Ansage wurde nicht gefunden.'
        );
    }

    require __DIR__ .
        '/../templates/measure-wizard-step-4.php';

    exit;
}

    http_response_code(400);

    exit(
        'Ungültiger Maßnahme-Wizard-Schritt.'
    );
}

if ($page === 'controllers') {

if ($action === 'stations') {

    $id = (int) ($_GET['id'] ?? 0);

    $controller =
        $controllersRepository->find($id);

    if (!$controller) {
        http_response_code(404);
        exit('Controller nicht gefunden.');
    }

    $stations =
        $controllersRepository->allStations();

    require __DIR__ .
        '/../templates/controller-stations.php';

    exit;
}    

    if ($action === 'new') {

        $controller = null;

        require __DIR__ .
            '/../templates/controller-form.php';

        exit;
    }

    if ($action === 'edit') {

        $id = (int) ($_GET['id'] ?? 0);

        $controller =
            $controllersRepository->find($id);

        if (!$controller) {
            http_response_code(404);
            exit('Controller nicht gefunden.');
        }

        require __DIR__ .
            '/../templates/controller-form.php';

        exit;
    }

    $search = trim(
        (string) ($_GET['search'] ?? '')
    );

    $controllers =
        $controllersRepository->all($search);

    require __DIR__ .
        '/../templates/controllers.php';

    exit;
}

if ($page === 'stations') {

    if ($action === 'new') {

        $station = null;

        require __DIR__ .
            '/../templates/station-form.php';

        exit;
    }

    if ($action === 'edit') {

        $id = (int) ($_GET['id'] ?? 0);

        $station = $stationsRepository->find($id);

        if (!$station) {
            http_response_code(404);
            exit('Haltestelle nicht gefunden.');
        }

        require __DIR__ .
            '/../templates/station-form.php';

        exit;
    }

    $search = trim(
        (string) ($_GET['search'] ?? '')
    );

    $stations = $stationsRepository->all($search);

    require __DIR__ .
        '/../templates/stations.php';

    exit;
}

$stationsCount = (int) $db
    ->query('SELECT COUNT(*) FROM stations')
    ->fetchColumn();

$linesCount = (int) $db
    ->query('SELECT COUNT(*) FROM lines')
    ->fetchColumn();

$controllersCount = (int) $db
    ->query('SELECT COUNT(*) FROM controllers')
    ->fetchColumn();

$audioFilesCount = (int) $db
    ->query('SELECT COUNT(*) FROM audio_files')
    ->fetchColumn();

$measures = $db
    ->query(
        'SELECT start_at, end_at, repeat_type, active
         FROM measures'
    )
    ->fetchAll(PDO::FETCH_ASSOC);

$measuresCount = count($measures);

$measuresActiveCount = 0;
$measuresPlannedCount = 0;
$measuresExpiredCount = 0;

$now = time();

foreach ($measures as $measure) {

    if ((int) $measure['active'] !== 1) {
        $measuresExpiredCount++;
        continue;
    }

    $startTimestamp = strtotime(
        (string) $measure['start_at']
    );

    $endTimestamp = null;

    if (!empty($measure['end_at'])) {
        $endTimestamp = strtotime(
            (string) $measure['end_at']
        );
    }

    if (
        $startTimestamp !== false &&
        $startTimestamp > $now
    ) {
        $measuresPlannedCount++;
        continue;
    }

    if (
        (
            (string) $measure['repeat_type'] === 'once' &&
            $startTimestamp !== false &&
            $startTimestamp + 5 < $now
        ) ||
        (
            (string) $measure['repeat_type'] !== 'once' &&
            $endTimestamp !== null &&
            $endTimestamp !== false &&
            $endTimestamp < $now
        )
    ) {
        $measuresExpiredCount++;
        continue;
    }

    $measuresActiveCount++;
}

?>
<!DOCTYPE html>

<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>ELA Server</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    font-family:
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    background: #f3f4f6;
    color: #111827;
}

header {
    background: #111827;
    color: white;
    padding: 20px 30px;
}

header h1 {
    margin: 0;
}

main {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.cards {
    display: grid;

    grid-template-columns:
        repeat(5, 1fr);

    gap: 20px;
}

.card {
    background: white;

    border-radius: 12px;

    padding: 24px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.08);
}

.card-title {
    color: #6b7280;
    font-size: 14px;
}

.card-value {
    margin-top: 8px;

    font-size: 36px;
    font-weight: 700;
}

.measure-status {
    display: flex;
    gap: 18px;
    margin-top: 10px;
    font-size: 20px;
    font-weight: 700;
}

.measure-status-labels {
    display: flex;
    gap: 18px;
    margin-top: 2px;
    font-size: 12px;
    font-weight: 400;
    color: #6b7280;
}

.measure-status-labels > div {
    flex: 1;
    text-align: center;
}

.measure-status > div {
    flex: 1;
    text-align: center;
}

.measure-status-active {
    color: #16a34a;
}

.measure-status-planned {
    color: #2563eb;
}

.measure-status-expired {
    color: #111827;
}

.navigation {
    margin-top: 30px;

    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(180px, 1fr));

    gap: 15px;
}

.navigation a {
    display: block;

    background: #2563eb;
    color: white;

    text-decoration: none;

    padding: 16px;

    border-radius: 10px;

    text-align: center;

    font-weight: 600;
}

.navigation a:hover {
    background: #1d4ed8;
}

</style>

</head>

<body>

<header>

<h1>ELA Server</h1>

</header>

<main>

<div class="cards">

<div class="card">

<div class="card-title">
Haltestellen
</div>

<div class="card-value">
<?= $stationsCount ?>
</div>

</div>

<div class="card">

<div class="card-title">
Linien
</div>

<div class="card-value">
<?= $linesCount ?>
</div>

</div>

<div class="card">

<div class="card-title">
Controller
</div>

<div class="card-value">
<?= $controllersCount ?>
</div>

</div>

<div class="card">

<div class="card-title">
Audio-Dateien
</div>

<div class="card-value">
<?= $audioFilesCount ?>
</div>

</div>

<div class="card">

<div class="card-title">
Maßnahmen
</div>

<div class="card-value">
<?= $measuresCount ?>
</div>

<div class="measure-status">

<div class="measure-status-active">
<?= $measuresActiveCount ?>
</div>

<div class="measure-status-planned">
<?= $measuresPlannedCount ?>
</div>

<div class="measure-status-expired">
<?= $measuresExpiredCount ?>
</div>

</div>

<div class="measure-status-labels">

<div>
Aktiv
</div>

<div>
Geplant
</div>

<div>
Abgelaufen
</div>

</div>

</div>

</div>

<div class="navigation">

<a href="/?page=stations">
Haltestellen
</a>

<a href="/?page=lines">
Linien
</a>

<a href="/?page=controllers">
Controller
</a>

<a href="/?page=audio">
Ansagen
</a>

<a href="/?page=measures">
Maßnahmen
</a>

</div>

</main>

</body>

</html>
