<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

use Tests\Acceptance\Support\App;

class AppFactory
{
    public static function make(): App
    {
        return new App();
    }
}
