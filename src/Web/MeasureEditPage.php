<?php

declare(strict_types=1);

namespace Ela\Web;

/**
 * Öffnet eine bestehende Maßnahme im Wizard (Schritt 1 mit den
 * gespeicherten Werten).
 */
final class MeasureEditPage extends Page
{
    public function get(string $action): void
    {
        $id = $this->number($_GET, 'id');

        if ($id <= 0) {
            $this->abort(400, 'Ungültige Maßnahme.');
        }

        $measures = $this->services->measures;
        $measure = $measures->find($id);

        if (!$measure) {
            $this->abort(404, 'Maßnahme nicht gefunden.');
        }

        $_SESSION['measure_wizard'] = [
            'edit_id' => $id,
            'name' => (string) $measure['name'],
            'audio_id' => (int) $measure['audio_id'],
            'active' => !empty($measure['active']),
            'targets' => $measures->targets($id),
            'schedule' => $this->scheduleOf($measure),
        ];

        (new MeasureWizardPage($this->services))->showStep(1);
    }

    /**
     * Wandelt die gespeicherte Maßnahme in das Zeitplan-Format des Wizards.
     *
     * @param array<string, mixed> $measure
     * @return array<string, mixed>
     */
    private function scheduleOf(array $measure): array
    {
        if ((string) $measure['repeat_type'] === 'once') {
            return ['type' => 'once'];
        }

        return [
            'type' => 'period',
            'start_at' => date('Y-m-d\TH:i', strtotime((string) $measure['start_at'])),
            'end_at' => !empty($measure['end_at'])
                ? date('Y-m-d\TH:i', strtotime((string) $measure['end_at']))
                : '',
            'interval' => (int) $measure['repeat_interval'],
            'disable_night' => !empty($measure['disable_night']),
        ];
    }
}
