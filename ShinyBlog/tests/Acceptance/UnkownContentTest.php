<?php
declare(strict_types=1);

namespace Tests\Acceptance;

use Nekudo\ShinyBlog\ShinyBlog;
use PHPUnit\Framework\TestCase;

class UnkownContentTest extends TestCase
{
    public function test_fetching_an_unknown_url()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/unknown-url';

        $config = require __DIR__.'/../../src/config.php';
        $app = new ShinyBlog($config);
        $response = $app->run();

        $this->assertEquals(404, $response->getStatusCode());
    }
}
