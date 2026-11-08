<?php

declare(strict_types=1);

namespace Ela;

use PDO;

/**
 * Ermittelt für eine Maßnahme, welcher Controller welche Ausgänge schalten
 * muss, und welche Zielhaltestellen sich nicht ansteuern lassen.
 *
 * Reine Lesezugriffe. Die Übertragung an die Controller (MQTT bzw. COM-Server)
 * kommt später und nutzt dieses Ergebnis.
 */
final class Dispatch
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    /**
     * @return array{
     *     controllers: list<array{controller_id: int, device_id: string, transport: string, pins: list<int>, stations: list<int>}>,
     *     unmapped: list<array{station_id: int, station_name: string, reason: string}>
     * }
     */
    public function planFor(int $measureId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                mt.station_id,
                s.name AS station_name,
                c.id AS controller_id,
                c.device_id,
                c.transport,
                c.enabled AS controller_enabled,
                cs.output_pin
             FROM measure_targets mt
             INNER JOIN stations s
                ON s.id = mt.station_id
             LEFT JOIN controller_stations cs
                ON cs.station_id = mt.station_id
             LEFT JOIN controllers c
                ON c.id = cs.controller_id
             WHERE mt.measure_id = :measure_id
             ORDER BY s.name, c.id'
        );

        $stmt->execute(['measure_id' => $measureId]);

        $targets = [];      // station_id => Name
        $mapped = [];       // station_id => true
        $reasons = [];      // station_id => Grund, warum (noch) nicht zugeordnet
        $controllers = [];  // controller_id => Eintrag

        foreach ($stmt->fetchAll() as $row) {
            $stationId = (int) $row['station_id'];
            $targets[$stationId] = (string) $row['station_name'];

            if ($row['controller_id'] === null) {
                $reasons[$stationId] = 'Kein Controller zugeordnet';
                continue;
            }

            if ((int) $row['controller_enabled'] !== 1) {
                $reasons[$stationId] = 'Controller ist deaktiviert';
                continue;
            }

            if ($row['output_pin'] === null) {
                $reasons[$stationId] = 'Kein Ausgang zugeordnet';
                continue;
            }

            $controllerId = (int) $row['controller_id'];

            $controllers[$controllerId] ??= [
                'controller_id' => $controllerId,
                'device_id' => (string) $row['device_id'],
                'transport' => (string) $row['transport'],
                'pins' => [],
                'stations' => [],
            ];

            $controllers[$controllerId]['pins'][] = (int) $row['output_pin'];
            $controllers[$controllerId]['stations'][] = $stationId;
            $mapped[$stationId] = true;
        }

        $unmapped = [];

        foreach ($targets as $stationId => $name) {
            if (!isset($mapped[$stationId])) {
                $unmapped[] = [
                    'station_id' => $stationId,
                    'station_name' => $name,
                    'reason' => $reasons[$stationId] ?? 'Unbekannt',
                ];
            }
        }

        foreach ($controllers as &$controller) {
            sort($controller['pins']);
            $controller['stations'] = array_values(array_unique($controller['stations']));
        }
        unset($controller);

        return [
            'controllers' => array_values($controllers),
            'unmapped' => $unmapped,
        ];
    }
}
