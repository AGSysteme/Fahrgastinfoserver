<?php

declare(strict_types=1);

namespace Ela\Web;

/**
 * Rendert ein Template aus templates/ mit den übergebenen Variablen.
 */
final class View
{
    public static function render(string $template, array $data): void
    {
        $file = dirname(__DIR__, 2) . '/templates/' . $template;

        (static function (string $__file, array $__data): void {
            extract($__data, EXTR_SKIP);
            require $__file;
        })($file, $data);
    }
}
