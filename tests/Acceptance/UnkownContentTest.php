<?php
declare(strict_types=1);

namespace Tests\Acceptance;

use PHPUnit\Framework\TestCase;
use Tests\Acceptance\Support\AppFactory;

class UnkownContentTest extends TestCase
{
    public function test_fetching_an_unknown_url()
    {
        $response = AppFactory::make()->visitUrl("/unknown-url");

        $this->assertEquals(404, $response->getStatusCode());
    }
}
