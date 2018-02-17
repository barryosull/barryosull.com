<?php namespace Tests\Acceptance;

use Nekudo\ShinyBlog\ShinyBlog;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function test_load_homepage()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $config = require __DIR__.'/../../src/config.php';
        $app = new ShinyBlog($config);
        $response = $app->run();

        $this->assertEquals(200, $response->getStatusCode());
    }
}