<?php namespace Tests\Acceptance;

use Nekudo\ShinyBlog\ShinyBlog;
use PHPUnit\Framework\TestCase;
use Tests\Acceptance\Support\AppFactory;

class IndexTest extends TestCase
{
    public function test_load_homepage()
    {
        $response = AppFactory::make()->visitUrl("/");

        $this->assertEquals(200, $response->getStatusCode());
    }
}