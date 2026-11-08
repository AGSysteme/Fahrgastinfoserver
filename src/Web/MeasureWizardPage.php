<?php

declare(strict_types=1);

namespace Ela\Web;

use RuntimeException;

/**
 * Maßnahmen-Wizard in 4 Schritten. Die Zwischenstände liegen in
 * $_SESSION['measure_wizard'].
 */
final class MeasureWizardPage extends Page
{
    private const SESSION_KEY = 'measure_wizard';

    public function post(string $action): void
    {
        match ($action) {
            'measure-wizard-step-1' => $this->saveStep1(),
            'measure-wizard-step-2' => $this->saveStep2(),
            'measure-wizard-step-3' => $this->saveStep3(),
            'measure-wizard-finish' => $this->finish(),
            default => null,
        };
    }

    public function get(string $action): void
    {
        if (isset($_GET['new']) && (string) $_GET['new'] === '1') {
            unset($_SESSION[self::SESSION_KEY]);
        }

        $this->showStep((int) ($_GET['step'] ?? 1));
    }

    /**
     * Zeigt den angegebenen Schritt an (beendet das Skript).
     */
    public function showStep(int $step): never
    {
        $wizard = $this->wizard();

        if ($step === 1) {
            $audioFiles = $this->services->audio->all();

            $selectedAudioId = !empty($wizard['audio_id'])
                ? (int) $wizard['audio_id']
                : (isset($_GET['audio_id']) ? (int) $_GET['audio_id'] : 0);

            $this->render('measure-wizard-step-1.php', [
                'audioFiles' => $audioFiles,
                'wizard' => $wizard ?? [],
                'selectedAudioId' => $selectedAudioId,
            ]);
        }

        if ($step === 2) {
            if (!$this->hasNameAndAudio($wizard)) {
                $this->redirect('/?page=measure-wizard&step=1');
            }

            $audio = $this->requireAudio((int) $wizard['audio_id']);

            $wizardLines = [];

            foreach ($this->services->lines->all() as $line) {
                if ((int) $line['active'] !== 1) {
                    continue;
                }

                $line['stations'] = $this->services->lines->stations((int) $line['id']);
                $wizardLines[] = $line;
            }

            $this->render('measure-wizard-step-2.php', [
                'wizard' => $wizard,
                'audio' => $audio,
                'savedTargets' => $wizard['targets'] ?? [],
                'stations' => $this->services->stations->all(),
                'wizardLines' => $wizardLines,
            ]);
        }

        if ($step === 3) {
            if (!$this->hasNameAudioAndTargets($wizard)) {
                $this->redirect('/?page=measure-wizard&step=1');
            }

            $audio = $this->requireAudio((int) $wizard['audio_id']);

            $this->render('measure-wizard-step-3.php', [
                'wizard' => $wizard,
                'audio' => $audio,
            ]);
        }

        if ($step === 4) {
            if (!$this->hasNameAudioAndTargets($wizard) || empty($wizard['schedule'])) {
                $this->redirect('/?page=measure-wizard&step=1');
            }

            $audio = $this->requireAudio((int) $wizard['audio_id']);

            $this->render('measure-wizard-step-4.php', [
                'wizard' => $wizard,
                'audio' => $audio,
                'stationsRepository' => $this->services->stations,
            ]);
        }

        $this->abort(400, 'Ungültiger Maßnahme-Wizard-Schritt.');
    }

    /**
     * Schritt 1: Name und Ansage. Übernimmt bei Bearbeitung die
     * bestehenden Ziele, Status und Zeitplan.
     */
    private function saveStep1(): void
    {
        $name = $this->text($_POST, 'name');
        $audioId = $this->number($_POST, 'audio_id');

        if ($name === '') {
            throw new RuntimeException('Der Name der Maßnahme ist erforderlich.');
        }

        if ($audioId <= 0) {
            throw new RuntimeException('Bitte eine Ansage auswählen.');
        }

        if (!$this->services->audio->find($audioId)) {
            throw new RuntimeException('Die ausgewählte Ansage wurde nicht gefunden.');
        }

        $editId = 0;
        $existingTargets = [];
        $existingActive = true;
        $existingSchedule = [];

        $current = $this->wizard();

        if ($current !== null) {
            $editId = (int) ($current['edit_id'] ?? 0);

            if (isset($current['targets']) && is_array($current['targets'])) {
                $existingTargets = $current['targets'];
            }

            if (array_key_exists('active', $current)) {
                $existingActive = (bool) $current['active'];
            }
        }

        if (isset($current['schedule']) && is_array($current['schedule'])) {
            $existingSchedule = $current['schedule'];
        }

        $_SESSION[self::SESSION_KEY] = [
            'name' => $name,
            'audio_id' => $audioId,
            'active' => $existingActive,
            'targets' => $existingTargets,
            'schedule' => $existingSchedule,
        ];

        if ($editId > 0) {
            $_SESSION[self::SESSION_KEY]['edit_id'] = $editId;
        }

        $this->redirect('/?page=measure-wizard&step=2');
    }

    /**
     * Schritt 2: Haltestellen (Ziele) wählen.
     */
    private function saveStep2(): void
    {
        $targets = $this->services->measures->validateTargets($_POST['stations'] ?? []);

        if (!$this->hasNameAndAudio($this->wizard())) {
            $this->redirect('/?page=measure-wizard&step=1');
        }

        $_SESSION[self::SESSION_KEY]['targets'] = $targets;

        $this->redirect('/?page=measure-wizard&step=3');
    }

    /**
     * Schritt 3: Wiedergabeart (einmalig oder wiederholt).
     */
    private function saveStep3(): void
    {
        $scheduleType = $this->text($_POST, 'schedule_type');

        if (!in_array($scheduleType, ['once', 'period'], true)) {
            throw new RuntimeException('Ungültige Wiedergabeart.');
        }

        if (!$this->hasNameAudioAndTargets($this->wizard())) {
            $this->redirect('/?page=measure-wizard&step=1');
        }

        if ($scheduleType === 'once') {
            $_SESSION[self::SESSION_KEY]['schedule'] = ['type' => 'once'];
        }

        if ($scheduleType === 'period') {
            $startAt = $this->text($_POST, 'start_at');
            $endAt = $this->text($_POST, 'end_at');
            $interval = $this->number($_POST, 'interval');

            Schedule::validatePeriod($startAt, $endAt, $interval);

            $_SESSION[self::SESSION_KEY]['schedule'] = [
                'type' => 'period',
                'start_at' => $startAt,
                'end_at' => $endAt,
                'interval' => $interval,
                'disable_night' => isset($_POST['disable_night']),
            ];
        }

        $this->redirect('/?page=measure-wizard&step=4');
    }

    /**
     * Schritt 4: Abschluss. Speichert die Maßnahme (neu oder Änderung).
     */
    private function finish(): void
    {
        $wizard = $this->wizard();

        if (
            $wizard === null ||
            empty($wizard['name']) ||
            empty($wizard['audio_id']) ||
            empty($wizard['targets']) ||
            empty($wizard['schedule'])
        ) {
            throw new RuntimeException('Die Daten der Maßnahme sind unvollständig.');
        }

        $targets = $wizard['targets'];
        $schedule = $wizard['schedule'];

        if (!is_array($targets)) {
            throw new RuntimeException('Die Ziele der Maßnahme sind ungültig.');
        }

        if (!is_array($schedule)) {
            throw new RuntimeException('Die Zeitplanung der Maßnahme ist ungültig.');
        }

        $scheduleType = (string) ($schedule['type'] ?? '');

        if ($scheduleType === 'once') {
            $startAt = date('Y-m-d H:i:s');
            $endAt = null;
            $repeatType = 'once';
            $repeatInterval = 1;
            $disableNight = false;
        } elseif ($scheduleType === 'period') {
            $startAt = trim((string) ($schedule['start_at'] ?? ''));
            $endAt = trim((string) ($schedule['end_at'] ?? ''));
            $repeatType = 'interval';
            $repeatInterval = (int) ($schedule['interval'] ?? 0);
            $disableNight = !empty($schedule['disable_night']);

            Schedule::validatePeriod($startAt, (string) $endAt, $repeatInterval);
        } else {
            throw new RuntimeException('Ungültige Zeitplanung.');
        }

        $measures = $this->services->measures;
        $editId = (int) ($wizard['edit_id'] ?? 0);

        if ($editId > 0) {
            $measures->updateWithTargets(
                $editId,
                (string) $wizard['name'],
                (int) $wizard['audio_id'],
                $startAt,
                $endAt,
                $repeatType,
                $repeatInterval,
                !empty($wizard['active']),
                $disableNight,
                $targets
            );
        } else {
            $measures->createWithTargets(
                (string) $wizard['name'],
                (int) $wizard['audio_id'],
                $startAt,
                $endAt,
                $repeatType,
                $repeatInterval,
                $disableNight,
                $targets
            );
        }

        unset($_SESSION[self::SESSION_KEY]);

        $this->redirect('/?page=measures');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function wizard(): ?array
    {
        $wizard = $_SESSION[self::SESSION_KEY] ?? null;

        return is_array($wizard) ? $wizard : null;
    }

    private function hasNameAndAudio(?array $wizard): bool
    {
        return $wizard !== null &&
            !empty($wizard['name']) &&
            !empty($wizard['audio_id']);
    }

    private function hasNameAudioAndTargets(?array $wizard): bool
    {
        return $this->hasNameAndAudio($wizard) && !empty($wizard['targets']);
    }

    private function requireAudio(int $audioId): array
    {
        $audio = $this->services->audio->find($audioId);

        if (!$audio) {
            throw new RuntimeException('Die ausgewählte Ansage wurde nicht gefunden.');
        }

        return $audio;
    }
}
