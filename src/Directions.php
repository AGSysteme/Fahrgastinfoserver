<?php

declare(strict_types=1);

namespace Ela;

use PDO;
use RuntimeException;

final class Directions
{
    public function __construct(
        private PDO $db
    ) {
    }

    public function allForLine(
        int $lineId
    ): array {

        $stmt = $this->db->prepare(
            'SELECT *
             FROM directions
             WHERE line_id = :line_id
             ORDER BY name'
        );

        $stmt->execute([
            'line_id' => $lineId
        ]);

        return $stmt->fetchAll();
    }

    public function find(
        int $id
    ): ?array {

        $stmt = $this->db->prepare(
            'SELECT *
             FROM directions
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $direction = $stmt->fetch();

        return $direction ?: null;
    }

    public function create(
        int $lineId,
        string $name,
        string $destination
    ): int {

        if ($lineId <= 0) {
            throw new RuntimeException(
                'Ungültige Linie.'
            );
        }

        $name = trim($name);
        $destination = trim($destination);

        if ($name === '') {
            throw new RuntimeException(
                'Der Name der Richtung ist erforderlich.'
            );
        }

        if ($destination === '') {
            throw new RuntimeException(
                'Das Ziel der Richtung ist erforderlich.'
            );
        }

        $stmt = $this->db->prepare(
            'INSERT INTO directions
                (line_id, name, destination)
             VALUES
                (:line_id, :name, :destination)'
        );

        try {

            $stmt->execute([
                'line_id' => $lineId,
                'name' => $name,
                'destination' => $destination
            ]);

        } catch (\PDOException $e) {

            if (
                str_contains(
                    $e->getMessage(),
                    'UNIQUE constraint failed'
                )
            ) {
                throw new RuntimeException(
                    'Diese Richtung existiert bereits für die Linie.'
                );
            }

            throw $e;
        }

        return (int) $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $name,
        string $destination,
        bool $active
    ): void {

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Richtung.'
            );
        }

        $name = trim($name);
        $destination = trim($destination);

        if ($name === '') {
            throw new RuntimeException(
                'Der Name der Richtung ist erforderlich.'
            );
        }

        if ($destination === '') {
            throw new RuntimeException(
                'Das Ziel der Richtung ist erforderlich.'
            );
        }

        $stmt = $this->db->prepare(
            'UPDATE directions
             SET
                name = :name,
                destination = :destination,
                active = :active
             WHERE id = :id'
        );

        try {

            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'destination' => $destination,
                'active' => $active ? 1 : 0
            ]);

        } catch (\PDOException $e) {

            if (
                str_contains(
                    $e->getMessage(),
                    'UNIQUE constraint failed'
                )
            ) {
                throw new RuntimeException(
                    'Diese Richtung existiert bereits für die Linie.'
                );
            }

            throw $e;
        }
    }

    public function delete(
        int $id
    ): void {

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Richtung.'
            );
        }

        $stmt = $this->db->prepare(
            'DELETE FROM directions
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);
    }
public function stations(int $directionId): array
{
    $stmt = $this->db->prepare(
        'SELECT
            ls.id,
            ls.sequence,
            s.id AS station_id,
            s.name,
            s.short_name,
            s.station_code
         FROM line_stations ls
         INNER JOIN stations s
            ON s.id = ls.station_id
         WHERE ls.direction_id = :direction_id
         ORDER BY ls.sequence ASC, s.name ASC'
    );

    $stmt->execute([
        'direction_id' => $directionId
    ]);

    return $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
}

public function availableStations(
    int $directionId
): array {

    $stmt = $this->db->prepare(
        'SELECT
            s.id,
            s.name,
            s.short_name,
            s.station_code
         FROM stations s
         WHERE s.active = 1
           AND s.id NOT IN (
               SELECT station_id
               FROM line_stations
               WHERE direction_id = :direction_id
           )
         ORDER BY s.name ASC'
    );

    $stmt->execute([
        'direction_id' => $directionId
    ]);

    return $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
}


public function addStation(
    int $directionId,
    int $stationId
): void {

    if ($directionId <= 0) {
        throw new RuntimeException(
            'Ungültige Richtung.'
        );
    }

    if ($stationId <= 0) {
        throw new RuntimeException(
            'Ungültige Haltestelle.'
        );
    }

    /*
     * Die nächste Position bestimmen.
     */
    $stmt = $this->db->prepare(
        'SELECT
            COALESCE(MAX(sequence), 0) + 1
         FROM line_stations
         WHERE direction_id = :direction_id'
    );

    $stmt->execute([
        'direction_id' => $directionId
    ]);

    $sequence = (int) $stmt->fetchColumn();

    /*
     * line_id aus der Richtung übernehmen.
     */
    $stmt = $this->db->prepare(
        'SELECT line_id
         FROM directions
         WHERE id = :direction_id'
    );

    $stmt->execute([
        'direction_id' => $directionId
    ]);

    $lineId = $stmt->fetchColumn();

    if ($lineId === false) {
        throw new RuntimeException(
            'Richtung nicht gefunden.'
        );
    }

    /*
     * Haltestelle hinzufügen.
     */
    $stmt = $this->db->prepare(
        'INSERT INTO line_stations
            (
                line_id,
                direction_id,
                station_id,
                sequence
            )
         VALUES
            (
                :line_id,
                :direction_id,
                :station_id,
                :sequence
            )'
    );

    $stmt->execute([
        'line_id' => (int) $lineId,
        'direction_id' => $directionId,
        'station_id' => $stationId,
        'sequence' => $sequence
    ]);
}


public function removeStation(
    int $directionId,
    int $stationId
): void {

    $stmt = $this->db->prepare(
        'DELETE FROM line_stations
         WHERE direction_id = :direction_id
           AND station_id = :station_id'
    );

    $stmt->execute([
        'direction_id' => $directionId,
        'station_id' => $stationId
    ]);
}

public function allActiveStations(): array
{
    $stmt = $this->db->query(
        'SELECT
            id,
            name,
            short_name,
            station_code
         FROM stations
         WHERE active = 1
         ORDER BY name'
    );

    return $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
}

public function replaceStations(
    int $directionId,
    array $stationIds
): void {

    if ($directionId <= 0) {
        throw new RuntimeException(
            'Ungültige Richtung.'
        );
    }

    $direction = $this->find($directionId);

    if (!$direction) {
        throw new RuntimeException(
            'Richtung nicht gefunden.'
        );
    }

    $lineId = (int) $direction['line_id'];

    if ($lineId <= 0) {
        throw new RuntimeException(
            'Ungültige Linie.'
        );
    }

    $this->db->beginTransaction();

    try {

        $stmt = $this->db->prepare(
            'DELETE FROM line_stations
             WHERE direction_id = :direction_id'
        );

        $stmt->execute([
            'direction_id' => $directionId
        ]);

        $stmt = $this->db->prepare(
            'INSERT INTO line_stations
                (
                    line_id,
                    direction_id,
                    station_id,
                    sequence
                )
             VALUES
                (
                    :line_id,
                    :direction_id,
                    :station_id,
                    :sequence
                )'
        );

        foreach (
            $stationIds as $index => $stationId
        ) {

            $stationId = (int) $stationId;

            if ($stationId <= 0) {
                continue;
            }

            $stmt->execute([
                'line_id' => $lineId,
                'direction_id' => $directionId,
                'station_id' => $stationId,
                'sequence' => $index + 1
            ]);
        }

        $this->db->commit();

    } catch (\Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

}
