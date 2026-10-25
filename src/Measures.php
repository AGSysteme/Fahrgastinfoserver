<?php

declare(strict_types=1);

namespace Ela;

use PDO;
use RuntimeException;

final class Measures
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT
                m.*,
                a.text AS audio_text,
                a.voice AS audio_voice,
                a.filename AS audio_filename
             FROM measures m
             INNER JOIN audio_files a
                ON a.id = m.audio_id
             ORDER BY m.start_at DESC, m.id DESC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT
                m.*,
                a.text AS audio_text,
                a.voice AS audio_voice,
                a.filename AS audio_filename
             FROM measures m
             INNER JOIN audio_files a
                ON a.id = m.audio_id
             WHERE m.id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $measure = $stmt->fetch();

        return $measure ?: null;
    }

    public function create(
        string $name,
        int $audioId,
        string $startAt,
        ?string $endAt,
        string $repeatType,
        int $repeatInterval,
        bool $disableNight
    ): int {

        $name = trim($name);
        $startAt = trim($startAt);
        $endAt = $endAt !== null
            ? trim($endAt)
            : null;

        $repeatType = trim($repeatType);

        if ($name === '') {
            throw new RuntimeException(
                'Der Name der Maßnahme darf nicht leer sein.'
            );
        }

        if ($audioId <= 0) {
            throw new RuntimeException(
                'Bitte eine Audiodatei auswählen.'
            );
        }

        if ($startAt === '') {
            throw new RuntimeException(
                'Ein Startzeitpunkt ist erforderlich.'
            );
        }

        if ($repeatType === '') {
            throw new RuntimeException(
                'Der Wiederholungstyp ist erforderlich.'
            );
        }

        if ($repeatInterval <= 0) {
            throw new RuntimeException(
                'Das Wiederholungsintervall muss größer als 0 sein.'
            );
        }

        $this->validateAudio($audioId);

        $stmt = $this->db->prepare(
            'INSERT INTO measures
                (
                    name,
                    audio_id,
                    start_at,
                    end_at,
                    repeat_type,
                    repeat_interval,
                    disable_night
                )
             VALUES
                (
                    :name,
                    :audio_id,
                    :start_at,
                    :end_at,
                    :repeat_type,
                    :repeat_interval,
                    :disable_night
                )'
        );

        $stmt->execute([
            'name' => $name,
            'audio_id' => $audioId,
            'start_at' => $startAt,
            'end_at' => $endAt !== ''
                ? $endAt
                : null,
            'repeat_type' => $repeatType,
            'repeat_interval' => $repeatInterval,
            'disable_night' => $disableNight ? 1 : 0
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createWithTargets(
        string $name,
        int $audioId,
        string $startAt,
        ?string $endAt,
        string $repeatType,
        int $repeatInterval,
        bool $disableNight,    
        array $targets
    ): int {

        if (!$targets) {
            throw new RuntimeException(
                'Es wurden keine Ziele ausgewählt.'
            );
        }

        $this->db->beginTransaction();

        try {

            $measureId =
                $this->create(
                    $name,
                    $audioId,
                    $startAt,
                    $endAt,
                    $repeatType,
                    $repeatInterval,
                    $disableNight
                );

            $this->addTargets($measureId, $targets);

            $check = $this->db->prepare(
                'SELECT COUNT(*)
                 FROM measure_targets
                 WHERE measure_id = :measure_id'
            );

            $check->execute([
                'measure_id' => $measureId
            ]);

            $targetCount =
                (int) $check->fetchColumn();

            if ($targetCount <= 0) {

                throw new RuntimeException(
                    'Es konnten keine Ziele der Maßnahme gespeichert werden.'
                );
            }

            $this->db->commit();

            return $measureId;

        } catch (\Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function validateTargets(mixed $targets): array
    {
        if (!is_array($targets) || !$targets) {
            throw new RuntimeException('Bitte mindestens eine Haltestelle auswählen.');
        }
        $ids = [];
        $check = $this->db->prepare('SELECT id FROM stations WHERE id = ?');
        foreach ($targets as $target) {
            if (!is_scalar($target) || !ctype_digit((string) $target) || (int) $target <= 0) {
                throw new RuntimeException('Ungültige Haltestellenauswahl.');
            }
            $id = (int) $target;
            $check->execute([$id]);
            if (!$check->fetchColumn()) throw new RuntimeException('Eine ausgewählte Haltestelle existiert nicht mehr.');
            $ids[$id] = $id;
        }
        return array_values($ids);
    }

    public function addTargets(int $measureId, array $targets): void
    {
        $targets = $this->validateTargets($targets);
        $stmt = $this->db->prepare('INSERT INTO measure_targets (measure_id, station_id) VALUES (?, ?)');
        foreach ($targets as $stationId) $stmt->execute([$measureId, $stationId]);
    }
    public function replaceTargets(
        int $measureId,
        array $targets
    ): void {

        if ($measureId <= 0) {
            throw new RuntimeException(
                'Ungültige Maßnahme.'
            );
        }

        $this->db->beginTransaction();

        try {

            $delete = $this->db->prepare(
                'DELETE FROM measure_targets
                 WHERE measure_id = :measure_id'
            );

            $delete->execute([
                'measure_id' => $measureId
            ]);

            $this->addTargets(
                $measureId,
                $targets
            );

            $check = $this->db->prepare(
                'SELECT COUNT(*)
                 FROM measure_targets
                 WHERE measure_id = :measure_id'
            );

            $check->execute([
                'measure_id' => $measureId
            ]);

            $targetCount =
                (int) $check->fetchColumn();

            if ($targetCount <= 0) {
                throw new RuntimeException(
                    'Es wurden keine Ziele ausgewählt.'
                );
            }

            $this->db->commit();

        } catch (\Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function update(
        int $id,
        string $name,
        int $audioId,
        string $startAt,
        ?string $endAt,
        string $repeatType,
        int $repeatInterval,
        bool $active,
        bool $disableNight = false
    ): void {

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Maßnahme.'
            );
        }

        $name = trim($name);
        $startAt = trim($startAt);
        $endAt = $endAt !== null
            ? trim($endAt)
            : null;

        $repeatType = trim($repeatType);

        if ($name === '') {
            throw new RuntimeException(
                'Der Name der Maßnahme darf nicht leer sein.'
            );
        }

        if ($audioId <= 0) {
            throw new RuntimeException(
                'Bitte eine Audiodatei auswählen.'
            );
        }

        if ($startAt === '') {
            throw new RuntimeException(
                'Ein Startzeitpunkt ist erforderlich.'
            );
        }

        if ($repeatType === '') {
            throw new RuntimeException(
                'Der Wiederholungstyp ist erforderlich.'
            );
        }

        if ($repeatInterval <= 0) {
            throw new RuntimeException(
                'Das Wiederholungsintervall muss größer als 0 sein.'
            );
        }

        $this->validateAudio($audioId);

        $stmt = $this->db->prepare(
            'UPDATE measures
             SET
                name = :name,
                audio_id = :audio_id,
                start_at = :start_at,
                end_at = :end_at,
                repeat_type = :repeat_type,
                repeat_interval = :repeat_interval,
                active = :active,
                disable_night = :disable_night,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'audio_id' => $audioId,
            'start_at' => $startAt,
            'end_at' => $endAt !== ''
                ? $endAt
                : null,
            'repeat_type' => $repeatType,
            'repeat_interval' => $repeatInterval,
            'active' => $active ? 1 : 0,
            'disable_night' => $disableNight ? 1 : 0
        ]);
    }

    public function updateWithTargets(
        int $id,
        string $name,
        int $audioId,
        string $startAt,
        ?string $endAt,
        string $repeatType,
        int $repeatInterval,
        bool $active,
        bool $disableNight,
        array $targets
    ): void {

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Maßnahme.'
            );
        }

        if (!$targets) {
            throw new RuntimeException(
                'Es wurden keine Ziele ausgewählt.'
            );
        }

        $this->db->beginTransaction();

        try {

            $this->update(
                $id,
                $name,
                $audioId,
                $startAt,
                $endAt,
                $repeatType,
                $repeatInterval,
                $active,
                $disableNight
            );

            $delete = $this->db->prepare(
                'DELETE FROM measure_targets
                 WHERE measure_id = :measure_id'
            );

            $delete->execute([
                'measure_id' => $id
            ]);

            $this->addTargets($id, $targets);

            $check = $this->db->prepare(
                'SELECT COUNT(*)
                 FROM measure_targets
                 WHERE measure_id = :measure_id'
            );

            $check->execute([
                'measure_id' => $id
            ]);

            $targetCount =
                (int) $check->fetchColumn();

            if ($targetCount <= 0) {
                throw new RuntimeException(
                    'Es konnten keine Ziele der Maßnahme gespeichert werden.'
                );
            }

            $this->db->commit();

        } catch (\Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

public function delete(int $id): void
{
    if ($id <= 0) {
        throw new RuntimeException(
            'Ungültige Maßnahme.'
        );
    }

    $measure = $this->find($id);

    if (!$measure) {
        throw new RuntimeException(
            'Maßnahme nicht gefunden.'
        );
    }

    $this->db->beginTransaction();

    try {

        $stmt = $this->db->prepare(
            'DELETE FROM measures
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $this->db->commit();

    } catch (\Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

public function targets(int $measureId): array
{
    $stmt = $this->db->prepare('SELECT station_id FROM measure_targets WHERE measure_id = ? ORDER BY id');
    $stmt->execute([$measureId]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}
    private function validateAudio(int $audioId): void
    {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM audio_files
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $audioId
        ]);

        if (!$stmt->fetchColumn()) {
            throw new RuntimeException(
                'Die ausgewählte Audiodatei wurde nicht gefunden.'
            );
        }
    }
}
