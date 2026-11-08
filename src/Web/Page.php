<?php

declare(strict_types=1);

namespace Ela\Web;

use Ela\Csrf;

/**
 * Basisklasse für alle Seiten.
 *
 * Ablauf pro Request:
 *   1. bei POST: post($action) – Formular verarbeiten, ggf. redirect()
 *   2. get($action) – Seite anzeigen (beendet das Skript)
 *
 * Fehler aus post() werden über setError() an die Anzeige weitergereicht.
 */
abstract class Page
{
    protected ?string $error = null;

    public function __construct(
        protected Services $services
    ) {
    }

    public function setError(string $message): void
    {
        $this->error = $message;
    }

    public function post(string $action): void
    {
    }

    abstract public function get(string $action): void;

    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = []): never
    {
        View::render(
            $template,
            $data + [
                'error' => $this->error,
                'csrfToken' => Csrf::token(),
            ]
        );

        exit;
    }

    protected function redirect(string $location): never
    {
        header('Location: ' . $location);

        exit;
    }

    protected function abort(int $status, string $message): never
    {
        http_response_code($status);

        exit($message);
    }

    /**
     * Gibt den Wert getrimmt als String zurück (fehlend = leer).
     */
    protected function text(array $source, string $key): string
    {
        return trim((string) ($source[$key] ?? ''));
    }

    /**
     * Gibt den Wert als Ganzzahl zurück (fehlend = 0).
     */
    protected function number(array $source, string $key): int
    {
        return (int) ($source[$key] ?? 0);
    }

    /**
     * Optionale Zahl: leer oder fehlend = null.
     */
    protected function optionalFloat(array $source, string $key): ?float
    {
        if (!isset($source[$key]) || $source[$key] === '') {
            return null;
        }

        return (float) $source[$key];
    }

    /**
     * Optionale Ganzzahl als Text: leer = null.
     */
    protected function optionalInt(string $value): ?int
    {
        return $value !== '' ? (int) $value : null;
    }
}
