<?php

declare(strict_types=1);

namespace Ela;

use PDO;

final class Controllers
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT
                    c.*,
                    cs.name AS com_server_name
                 FROM controllers c
                 LEFT JOIN com_servers cs
                    ON cs.id = c.com_server_id
                 WHERE c.name LIKE :search
                    OR c.device_id LIKE :search
                    OR c.mqtt_client_id LIKE :search
                 ORDER BY c.name'
            );

            $stmt->execute([
                'search' => '%' . $search . '%'
            ]);

            return $stmt->fetchAll();
        }

        return $this->db
            ->query(
                'SELECT
                    c.*,
                    cs.name AS com_server_name
                 FROM controllers c
                 LEFT JOIN com_servers cs
                    ON cs.id = c.com_server_id
                 ORDER BY c.name'
            )
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                c.*,
                cs.name AS com_server_name
             FROM controllers c
             LEFT JOIN com_servers cs
                ON cs.id = c.com_server_id
             WHERE c.id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $controller = $stmt->fetch();

        return $controller ?: null;
    }

    /**
     * Alle verfügbaren COM-Server laden.
     */
    public function comServers(): array
    {
        return $this->db
            ->query(
                'SELECT
                    id,
                    name,
                    host,
                    tcp_port,
                    enabled
                 FROM com_servers
                 ORDER BY name'
            )
            ->fetchAll();
    }

public function create(
    string $name,
    string $deviceId,
    string $mqttClientId,
    string $mqttTopic,
    ?string $ipAddress,
    ?int $comServerId = null,
    ?int $serialPort = null,
    ?int $rs485Address = null
): int {
    $stmt = $this->db->prepare(
        'INSERT INTO controllers
            (
                name,
                device_id,
                mqtt_client_id,
                mqtt_topic,
                ip_address,
                com_server_id,
                serial_port,
                rs485_address,
                enabled
            )
         VALUES
            (
                :name,
                :device_id,
                :mqtt_client_id,
                :mqtt_topic,
                :ip_address,
                :com_server_id,
                :serial_port,
                :rs485_address,
                1
            )'
    );

    try {
        $stmt->execute([
            'name' => $name,
            'device_id' => $deviceId,
            'mqtt_client_id' => $mqttClientId !== ''
                ? $mqttClientId
                : null,
            'mqtt_topic' => $mqttTopic !== ''
                ? $mqttTopic
                : null,
            'ip_address' => $ipAddress !== ''
                ? $ipAddress
                : null,
            'com_server_id' => $comServerId,
            'serial_port' => $serialPort,
            'rs485_address' => $rs485Address
        ]);
    } catch (\PDOException $e) {

        if (
            str_contains(
                $e->getMessage(),
                'UNIQUE constraint failed: controllers.com_server_id, controllers.serial_port, controllers.rs485_address'
            )
        ) {
            throw new \RuntimeException(
                'Diese Kombination aus COM-Server, seriellem Port und RS485-Adresse ist bereits einem anderen Controller zugeordnet.'
            );
        }

        if (
            str_contains(
                $e->getMessage(),
                'controllers.name'
            )
        ) {
            throw new \RuntimeException(
                'Der Controller-Name ist bereits vergeben.'
            );
        }

        if (
            str_contains(
                $e->getMessage(),
                'controllers.device_id'
            )
        ) {
            throw new \RuntimeException(
                'Die Geräte-ID ist bereits vergeben.'
            );
        }

        if (
            str_contains(
                $e->getMessage(),
                'controllers.mqtt_client_id'
            )
        ) {
            throw new \RuntimeException(
                'Die MQTT Client-ID ist bereits vergeben.'
            );
        }

        throw $e;
    }

    return (int) $this->db->lastInsertId();
}

public function update(
    int $id,
    string $name,
    string $deviceId,
    string $mqttClientId,
    string $mqttTopic,
    ?string $ipAddress,
    bool $enabled,
    ?int $comServerId = null,
    ?int $serialPort = null,
    ?int $rs485Address = null
): void {
    $stmt = $this->db->prepare(
        'UPDATE controllers
         SET
            name = :name,
            device_id = :device_id,
            mqtt_client_id = :mqtt_client_id,
            mqtt_topic = :mqtt_topic,
            ip_address = :ip_address,
            com_server_id = :com_server_id,
            serial_port = :serial_port,
            rs485_address = :rs485_address,
            enabled = :enabled,
            updated_at = CURRENT_TIMESTAMP
         WHERE id = :id'
    );

    try {
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'device_id' => $deviceId,
            'mqtt_client_id' => $mqttClientId !== ''
                ? $mqttClientId
                : null,
            'mqtt_topic' => $mqttTopic !== ''
                ? $mqttTopic
                : null,
            'ip_address' => $ipAddress !== ''
                ? $ipAddress
                : null,
            'com_server_id' => $comServerId,
            'serial_port' => $serialPort,
            'rs485_address' => $rs485Address,
            'enabled' => $enabled ? 1 : 0
        ]);
    } catch (\PDOException $e) {

        if (
            str_contains(
                $e->getMessage(),
                'UNIQUE constraint failed: controllers.com_server_id, controllers.serial_port, controllers.rs485_address'
            )
        ) {
            throw new \RuntimeException(
                'Diese Kombination aus COM-Server, seriellem Port und RS485-Adresse ist bereits einem anderen Controller zugeordnet.'
            );
        }

        if (
            str_contains(
                $e->getMessage(),
                'controllers.name'
            )
        ) {
            throw new \RuntimeException(
                'Der Controller-Name ist bereits vergeben.'
            );
        }

        if (
            str_contains(
                $e->getMessage(),
                'controllers.device_id'
            )
        ) {
            throw new \RuntimeException(
                'Die Geräte-ID ist bereits vergeben.'
            );
        }

        if (
            str_contains(
                $e->getMessage(),
                'controllers.mqtt_client_id'
            )
        ) {
            throw new \RuntimeException(
                'Die MQTT Client-ID ist bereits vergeben.'
            );
        }

        throw $e;
    }
}

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM controllers
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);
    }

    public function stations(int $controllerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*
             FROM stations s
             INNER JOIN controller_stations cs
                 ON cs.station_id = s.id
             WHERE cs.controller_id = :controller_id
             ORDER BY s.name'
        );

        $stmt->execute([
            'controller_id' => $controllerId
        ]);

        return $stmt->fetchAll();
    }

    public function assignStation(
        int $controllerId,
        int $stationId
    ): void {
        $stmt = $this->db->prepare(
            'INSERT OR IGNORE INTO controller_stations
                (controller_id, station_id)
             VALUES
                (:controller_id, :station_id)'
        );

        $stmt->execute([
            'controller_id' => $controllerId,
            'station_id' => $stationId
        ]);
    }

    public function removeStation(
        int $controllerId,
        int $stationId
    ): void {
        $stmt = $this->db->prepare(
            'DELETE FROM controller_stations
             WHERE controller_id = :controller_id
               AND station_id = :station_id'
        );

        $stmt->execute([
            'controller_id' => $controllerId,
            'station_id' => $stationId
        ]);
    }

    public function allStations(): array
    {
        return $this->db
            ->query(
                'SELECT
                    s.*,
                    cs.controller_id AS assigned_controller_id
                 FROM stations s
                 LEFT JOIN controller_stations cs
                    ON cs.station_id = s.id
                 ORDER BY s.name'
            )
            ->fetchAll();
    }

    public function replaceStations(
        int $controllerId,
        array $stationIds
    ): void {
        $this->db->beginTransaction();

        try {

            /*
             * Bisherige Zuordnungen dieses Controllers löschen.
             */
            $stmt = $this->db->prepare(
                'DELETE FROM controller_stations
                 WHERE controller_id = :controller_id'
            );

            $stmt->execute([
                'controller_id' => $controllerId
            ]);

            /*
             * Neue Zuordnungen speichern.
             */
            $stmt = $this->db->prepare(
                'INSERT INTO controller_stations
                    (
                        controller_id,
                        station_id
                    )
                 VALUES
                    (
                        :controller_id,
                        :station_id
                    )'
            );

            foreach ($stationIds as $stationId) {

                $stationId = (int) $stationId;

                if ($stationId <= 0) {
                    continue;
                }

                $stmt->execute([
                    'controller_id' => $controllerId,
                    'station_id' => $stationId
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
