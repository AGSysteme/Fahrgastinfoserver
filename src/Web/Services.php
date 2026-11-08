<?php

declare(strict_types=1);

namespace Ela\Web;

use Ela\Audio;
use Ela\ComServers;
use Ela\Controllers;
use Ela\Lines;
use Ela\Measures;
use Ela\Stations;

/**
 * Bündelt die Repositories, die die Seiten-Controller brauchen.
 */
final class Services
{
    public function __construct(
        public readonly Stations $stations,
        public readonly Lines $lines,
        public readonly Controllers $controllers,
        public readonly Audio $audio,
        public readonly Measures $measures,
        public readonly ComServers $comServers,
    ) {
    }
}
