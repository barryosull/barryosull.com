<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

class AppFactory
{
    public static function make(): App
    {
        return new AppHttp();
    }
}
