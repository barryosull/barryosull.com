<?php
declare(strict_types=1);

namespace Tests\Acceptance;

class AppFactory
{
    public static function make(): App
    {
        return new App();
    }
}
