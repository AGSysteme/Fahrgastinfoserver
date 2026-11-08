<?php

declare(strict_types=1);

namespace Ela;

use PDO;
use RuntimeException;

/**
 * Befehlswarteschlange für Controller. Die Oberfläche legt Befehle an,
 * die Bridge sendet sie per MQTT und trägt die Quittungen ein.
 */
final class DeviceCommands
{
    /** Sicherheitsreserve nach der Ansagendauer, siehe docs/mqtt.md */
    public const WATCHDOG_MARGIN_MS = 5000;

    public function __construct(
        private readonly PDO $db
    ) {
    }

    /**
     * @param list<int> $pins  Ausgänge 1–16, leer = nur Audio
     */
    public function queuePlay(int $controllerId, int $audioId, array $pins, int $durationMs): int
    {
        if ($audioId < 0 || $audioId > 9999) {
            throw new RuntimeException('Ungültige Ansagen-ID.');
        }

        if ($durationMs <= 0) {
            throw new RuntimeException('Die Dauer der Ansage ist unbekannt.');
        }

        $payload = [
            'audio' => $audioId,
            'pins' => array_values($pins),
            'duration_ms' => $durationMs,
            'max_ms' => $durationMs + self::WATCHDOG_MARGIN_MS,
        ];

        return $this->insert($controllerId, 'play', $payload);
    }

    public function queueStop(int $controllerId): int
    {
        return $this->insert($controllerId, 'stop', []);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recent(int $controllerId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, command, payload, status, detail, created_at, sent_at, acked_at
             FROM device_commands
             WHERE controller_id = :controller_id
             ORDER BY id DESC
             LIMIT ' . max(1, min(100, $limit))
        );

        $stmt->execute(['controller_id' => $controllerId]);

        return $stmt->fetchAll();
    }

    /**
     * Liest die Dauer einer WAV-Datei aus dem Header (Millisekunden).
     */
    public static function wavDurationMs(string $path): int
    {
        $fh = @fopen($path, 'rb');

        if ($fh === false) {
            throw new RuntimeException('Audiodatei nicht gefunden: ' . basename($path));
        }

        try {
            if (fread($fh, 4) !== 'RIFF') {
                throw new RuntimeException('Keine gültige WAV-Datei: ' . basename($path));
            }

            fread($fh, 4);

            if (fread($fh, 4) !== 'WAVE') {
                throw new RuntimeException('Keine gültige WAV-Datei: ' . basename($path));
            }

            $byteRate = null;
            $dataSize = null;

            while (!feof($fh) && ($byteRate === null || $dataSize === null)) {
                $chunkId = fread($fh, 4);
                $sizeBytes = fread($fh, 4);

                if (strlen((string) $chunkId) < 4 || strlen((string) $sizeBytes) < 4) {
                    break;
                }

                $size = unpack('V', $sizeBytes)[1];

                if ($chunkId === 'fmt ') {
                    $fmt = fread($fh, $size);
                    $byteRate = unpack('V', substr((string) $fmt, 8, 4))[1];
                    continue;
                }

                if ($chunkId === 'data') {
                    $dataSize = $size;
                    break;
                }

                fseek($fh, $size + ($size % 2), SEEK_CUR);
            }

            if (!$byteRate || $dataSize === null) {
                throw new RuntimeException('WAV-Header unvollständig: ' . basename($path));
            }

            return (int) round($dataSize * 1000 / $byteRate);
        } finally {
            fclose($fh);
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function insert(int $controllerId, string $command, array $payload): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO device_commands (controller_id, command, payload)
             VALUES (:controller_id, :command, :payload)'
        );

        $stmt->execute([
            'controller_id' => $controllerId,
            'command' => $command,
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
        ]);

        return (int) $this->db->lastInsertId();
    }
}
