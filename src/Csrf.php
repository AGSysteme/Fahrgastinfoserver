<?php

declare(strict_types=1);

namespace Ela;

final class Csrf
{
    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (
            !isset($_SESSION['csrf_token']) ||
            !is_string($_SESSION['csrf_token'])
        ) {
            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        return $_SESSION['csrf_token'];
    }

    public static function verify(?string $token): bool
    {
        if (
            session_status() !== PHP_SESSION_ACTIVE ||
            !isset($_SESSION['csrf_token']) ||
            !is_string($token)
        ) {
            return false;
        }

        return hash_equals(
            $_SESSION['csrf_token'],
            $token
        );
    }
}
