<?php

declare(strict_types=1);

namespace Ela;

use PDO;
use PDOException;
use RuntimeException;

final class ComServers
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    /**
     * Alle COM-Server mit der Anzahl der zugeordneten Controller.
     */
    public function all(): array
    {
        return $this->db
            ->query(
                'SELECT
                    cs.*,
                    COUNT(c.id) AS controller_count
                 FROM com_servers cs
                 LEFT JOIN controllers c
                    ON c.com_server_id = cs.id
                 GROUP BY cs.id
                 ORDER BY cs.name'
            )
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM com_servers
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $server = $stmt->fetch();

        return $server ?: null;
    }

    public function usageCount(int $id): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM controllers
             WHERE com_server_id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function create(
        string $name,
        string $host,
        int $tcpPort,
        bool $enabled
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO com_servers
                (name, host, tcp_port, enabled)
             VALUES
                (:name, :host, :tcp_port, :enabled)'
        );

        try {
            $stmt->execute([
                'name' => $name,
                'host' => $host,
                'tcp_port' => $tcpPort,
                'enabled' => $enabled ? 1 : 0
            ]);
        } catch (PDOException $e) {
            throw $this->duplicateError($e);
        }

        return (int) $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $name,
        string $host,
        int $tcpPort,
        bool $enabled
    ): void {
        $stmt = $this->db->prepare(
            'UPDATE com_servers
             SET
                name = :name,
                host = :host,
                tcp_port = :tcp_port,
                enabled = :enabled,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        try {
            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'host' => $host,
                'tcp_port' => $tcpPort,
                'enabled' => $enabled ? 1 : 0
            ]);
        } catch (PDOException $e) {
            throw $this->duplicateError($e);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM com_servers
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);
    }

    private function duplicateError(PDOException $e): \Throwable
    {
        return match (Database::duplicateKey($e)) {
            'name' => new RuntimeException('Der Name ist bereits vergeben.'),
            'host' => new RuntimeException('Dieser Host ist bereits als COM-Server angelegt.'),
            default => $e,
        };
    }
}
