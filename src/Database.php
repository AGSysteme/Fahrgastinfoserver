<?php

declare(strict_types=1);

namespace Ela;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $pdo = new PDO(
                'sqlite:/opt/ela/data/ela.db'
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            $pdo->exec('PRAGMA foreign_keys = ON');

            self::$connection = $pdo;
        }

        return self::$connection;
    }
}
