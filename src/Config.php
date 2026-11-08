<?php

declare(strict_types=1);

namespace Ela;

/**
 * Zentrale Konfiguration. Jeder Wert kann per Umgebungsvariable
 * überschrieben werden (z. B. für Tests mit einer Kopie der Datenbank).
 */
final class Config
{
    public static function dbPath(): string
    {
        return self::env('ELA_DB_PATH', '/opt/ela/data/ela.db');
    }

    public static function audioDir(): string
    {
        return self::env('ELA_AUDIO_DIR', '/opt/ela/audio/announcements');
    }

    public static function modelsDir(): string
    {
        return self::env('ELA_MODELS_DIR', '/opt/ela/models');
    }

    public static function piperBin(): string
    {
        return self::env('ELA_PIPER_BIN', '/opt/ela/piper-venv/bin/piper');
    }

    private static function env(string $name, string $default): string
    {
        $value = getenv($name);

        return ($value === false || $value === '') ? $default : $value;
    }
}
