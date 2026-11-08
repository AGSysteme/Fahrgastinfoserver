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
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                Config::dbHost(),
                Config::dbPort(),
                Config::dbName()
            );

            // ATTR_EMULATE_PREPARES bleibt an: der Code verwendet :search
            // mehrfach in einer Abfrage, das geht nur mit emulierten Statements.
            $pdo = new PDO(
                $dsn,
                Config::dbUser(),
                Config::dbPassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // SQLite-CURRENT_TIMESTAMP war UTC; die Werte sollen gleich bleiben.
            $pdo->exec("SET time_zone = '+00:00'");

            self::$connection = $pdo;
        }

        return self::$connection;
    }

    /**
     * Liefert bei einer UNIQUE-Verletzung den Namen des Schlüssels, sonst null.
     * MariaDB meldet z. B.: "Duplicate entry 'x' for key 'name'".
     */
    public static function duplicateKey(\PDOException $e): ?string
    {
        if (($e->errorInfo[1] ?? null) !== 1062) {
            return null;
        }

        if (preg_match("/for key '([^']+)'/", $e->getMessage(), $m) === 1) {
            return $m[1];
        }

        return null;
    }
}
