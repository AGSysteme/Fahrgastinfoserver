<?php

declare(strict_types=1);

namespace Ela;

use PDO;

final class Stations
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT *
                 FROM stations
                 WHERE name LIKE :search
                    OR short_name LIKE :search
                    OR station_code LIKE :search
                 ORDER BY name'
            );

            $stmt->execute([
                'search' => '%' . $search . '%'
            ]);

            return $stmt->fetchAll();
        }

        return $this->db
            ->query(
                'SELECT *
                 FROM stations
                 ORDER BY name'
            )
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM stations
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $station = $stmt->fetch();

        return $station ?: null;
    }

    public function create(
        string $name,
        string $shortName,
        string $stationCode,
        ?float $latitude,
        ?float $longitude
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO stations
                (name, short_name, station_code, latitude, longitude)
             VALUES
                (:name, :short_name, :station_code, :latitude, :longitude)'
        );

        $stmt->execute([
            'name' => $name,
            'short_name' => $shortName !== '' ? $shortName : null,
            'station_code' => $stationCode !== '' ? $stationCode : null,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $name,
        string $shortName,
        string $stationCode,
        ?float $latitude,
        ?float $longitude,
        bool $active
    ): void {
        $stmt = $this->db->prepare(
            'UPDATE stations
             SET
                name = :name,
                short_name = :short_name,
                station_code = :station_code,
                latitude = :latitude,
                longitude = :longitude,
                active = :active,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'short_name' => $shortName !== '' ? $shortName : null,
            'station_code' => $stationCode !== '' ? $stationCode : null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'active' => $active ? 1 : 0
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM stations
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);
    }
}
