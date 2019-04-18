<?php namespace Tests\Acceptance;

use PHPUnit\Framework\TestCase;
use Tests\Acceptance\Support\AppFactory;

class IndexTest extends TestCase
{
    /**
     * @test
     */
    public function homepage_loads()
    {
        $response = AppFactory::make()->visitUrl("/");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains(">barryosull.com</a>", strval($response->getBody()));
    }
}