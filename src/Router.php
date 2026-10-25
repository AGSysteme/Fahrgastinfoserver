<?php

declare(strict_types=1);

namespace Ela;

final class Router
{
    public function page(): string
    {
        if (isset($_POST['page'])) {
            return (string) $_POST['page'];
        }

        if (isset($_GET['page'])) {
            return (string) $_GET['page'];
        }

        return 'dashboard';
    }

    public function action(): string
    {
        if (isset($_POST['action'])) {
            return (string) $_POST['action'];
        }

        if (isset($_GET['action'])) {
            return (string) $_GET['action'];
        }

        return 'list';
    }
}
