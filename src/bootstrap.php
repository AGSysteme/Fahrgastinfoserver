<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');

/*
 * Autoloader für den Namespace Ela\ (PSR-4-ähnlich, Basis: src/).
 * Ela\Web\StationsPage -> src/Web/StationsPage.php
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'Ela\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

session_start();
