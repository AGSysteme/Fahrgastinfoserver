<?php

declare(strict_types=1);

namespace Ela\Web;

use DateTime;
use RuntimeException;

/**
 * Prüfung der Zeitplanung einer Maßnahme (Zeitraum mit Wiederholung).
 */
final class Schedule
{
    public const INTERVALS = [5, 10, 30, 60];

    private const FORMAT = 'Y-m-d\TH:i';

    /**
     * @throws RuntimeException bei ungültigem Intervall oder Zeitraum
     */
    public static function validatePeriod(
        string $startAt,
        string $endAt,
        int $interval
    ): void {
        if (!in_array($interval, self::INTERVALS, true)) {
            throw new RuntimeException('Ungültiges Intervall.');
        }

        $start = DateTime::createFromFormat(self::FORMAT, $startAt);
        $end = DateTime::createFromFormat(self::FORMAT, $endAt);

        if (!$start || $start->format(self::FORMAT) !== $startAt) {
            throw new RuntimeException('Der Beginn ist ungültig.');
        }

        if (!$end || $end->format(self::FORMAT) !== $endAt) {
            throw new RuntimeException('Das Ende ist ungültig.');
        }

        if ($end <= $start) {
            throw new RuntimeException('Das Ende muss nach dem Beginn liegen.');
        }
    }
}
