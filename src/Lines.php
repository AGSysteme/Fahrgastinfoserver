<?php

declare(strict_types=1);

namespace Ela;

use PDO;
use RuntimeException;

final class Lines
{
    public function __construct(
        private PDO $db
    ) {
    }

    public function all(
        string $search = ''
    ): array {

        if ($search !== '') {

            $stmt = $this->db->prepare(
                'SELECT *
                 FROM lines
                 WHERE number LIKE :search
                    OR name LIKE :search
                 ORDER BY number'
            );

            $stmt->execute([
                'search' => '%' . $search . '%'
            ]);

            return $stmt->fetchAll();
        }

        return $this->db
            ->query(
                'SELECT *
                 FROM lines
                 ORDER BY number'
            )
            ->fetchAll();
    }

    public function find(
        int $id
    ): ?array {

        $stmt = $this->db->prepare(
            'SELECT *
             FROM lines
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $line = $stmt->fetch();

        return $line ?: null;
    }

    public function create(
        string $number,
        string $name,
        string $description
    ): int {

        $number = trim($number);
        $name = trim($name);
        $description = trim($description);

        if ($number === '') {
            throw new RuntimeException(
                'Die Liniennummer ist erforderlich.'
            );
        }

        $stmt = $this->db->prepare(
            'INSERT INTO lines
                (number, name, description)
             VALUES
                (:number, :name, :description)'
        );

        try {

            $stmt->execute([
                'number' => $number,
                'name' => $name !== ''
                    ? $name
                    : null,
                'description' => $description !== ''
                    ? $description
                    : null
            ]);

        } catch (\PDOException $e) {

            if (
                str_contains(
                    $e->getMessage(),
                    'UNIQUE constraint failed'
                )
            ) {
                throw new RuntimeException(
                    'Diese Liniennummer existiert bereits.'
                );
            }

            throw $e;
        }

        return (int) $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $number,
        string $name,
        string $description,
        bool $active
    ): void {

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Linie.'
            );
        }

        $number = trim($number);
        $name = trim($name);
        $description = trim($description);

        if ($number === '') {
            throw new RuntimeException(
                'Die Liniennummer ist erforderlich.'
            );
        }

        $stmt = $this->db->prepare(
            'UPDATE lines
             SET
                number = :number,
                name = :name,
                description = :description,
                active = :active,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        try {

            $stmt->execute([
                'id' => $id,
                'number' => $number,
                'name' => $name !== ''
                    ? $name
                    : null,
                'description' => $description !== ''
                    ? $description
                    : null,
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
                    'Diese Liniennummer existiert bereits.'
                );
            }

            throw $e;
        }
    }

    public function stations(int $lineId): array
    {
        $stmt = $this->db->prepare('SELECT s.*, s.id AS station_id, ls.sequence
            FROM line_stations ls JOIN stations s ON s.id = ls.station_id
            WHERE ls.line_id = ? ORDER BY ls.sequence, s.id');
        $stmt->execute([$lineId]);
        return $stmt->fetchAll();
    }

    public function replaceStations(int $lineId, mixed $stationIds): void
    {
        if (!$this->find($lineId)) throw new RuntimeException('Linie nicht gefunden.');
        if (!is_array($stationIds)) throw new RuntimeException('Ungültige Haltestellenauswahl.');
        $ids = [];
        foreach ($stationIds as $id) {
            if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id <= 0) {
                throw new RuntimeException('Ungültige Haltestelle.');
            }
            $ids[(int) $id] = (int) $id;
        }
        $this->db->beginTransaction();
        try {
            $this->db->prepare('DELETE FROM line_stations WHERE line_id = ?')->execute([$lineId]);
            $insert = $this->db->prepare('INSERT INTO line_stations (line_id, station_id, sequence) VALUES (?, ?, ?)');
            foreach (array_values($ids) as $index => $id) $insert->execute([$lineId, $id, $index + 1]);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(
        int $id
    ): void {

        if ($id <= 0) {
            throw new RuntimeException(
                'Ungültige Linie.'
            );
        }

        $stmt = $this->db->prepare(
            'DELETE FROM lines
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);
    }
}
