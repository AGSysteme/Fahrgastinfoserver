<?php

declare(strict_types=1);

namespace Ela\Web;

/**
 * Zwei-Spalten-Auswahl für Haltestellen (verfügbar | ausgewählt).
 * Das Verschieben per Drag & Drop übernimmt das JavaScript der Seite.
 */
final class StationPicker
{
    /**
     * Bringt Zeilen aus der Datenbank in die Form, die render() erwartet.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return list<array{id: int, name: string, active: int}>
     */
    public static function items(array $rows, string $idKey): array
    {
        return array_map(
            static fn (array $row): array => [
                'id' => (int) $row[$idKey],
                'name' => (string) $row['name'],
                'active' => (int) $row['active'],
            ],
            $rows
        );
    }

    /**
     * @param list<array{id: int, name: string, active: int}> $items
     * @param list<int> $selected
     */
    public static function render(array $items, array $selected): string
    {
        $available = '';
        $chosen = '';

        foreach ($items as $item) {
            $html = self::station($item);

            if (in_array($item['id'], $selected, true)) {
                $chosen .= $html;
            } else {
                $available .= $html;
            }
        }

        return '<div class="picker">'
            . '<div class="row g-3">'
            . self::column(
                'available',
                'Verfügbare Haltestellen',
                'Haltestellen hier greifen und rechts hineinziehen.',
                $available,
                'Alle Haltestellen sind ausgewählt.'
            )
            . self::column(
                'selected',
                'Ausgewählte Haltestellen',
                'Hier hineinziehen, um sie auszuwählen.',
                $chosen,
                'Noch keine Haltestelle ausgewählt.'
            )
            . '</div>'
            . '<div class="d-flex flex-wrap gap-2 mt-3">'
            . '<button type="button" class="btn btn-sm btn-outline-secondary" data-picker-action="deselect">Alle abwählen</button>'
            . '</div>'
            . '</div>';
    }

    /**
     * @param array{id: int, name: string, active: int} $item
     */
    private static function station(array $item): string
    {
        return '<div class="station" draggable="true" data-station-id="' . $item['id'] . '">'
            . '<div class="handle">☰</div>'
            . '<div class="station-info"><div class="station-name">'
            . htmlspecialchars($item['name'], ENT_QUOTES)
            . ($item['active'] !== 1 ? ' <small class="text-muted">(inaktiv)</small>' : '')
            . '</div></div></div>';
    }

    private static function column(
        string $zone,
        string $title,
        string $hint,
        string $content,
        string $empty
    ): string {
        return '<div class="col-12 col-lg-6"><div class="card h-100"><div class="card-body p-3 p-md-4">'
            . '<h3 class="h5 mb-1">' . $title . '</h3>'
            . '<p class="text-muted small mb-3">' . $hint . '</p>'
            . '<div class="station-list" data-zone="' . $zone . '">'
            . $content
            . '<div class="drop-zone-empty d-none">' . $empty . '</div>'
            . '</div></div></div></div>';
    }
}
