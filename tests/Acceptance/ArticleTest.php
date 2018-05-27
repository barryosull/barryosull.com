<?php namespace Tests\Acceptance;

use Nekudo\ShinyBlog\ShinyBlog;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function test_load_article()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/blog/acceptance-testing-your-php-app-with-ease';

        $config = require __DIR__.'/../../src/config.php';
        $app = new ShinyBlog($config);
        $response = $app->run();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains("<h1>Acceptance testing your PHP app with ease</h1>", $response->getBody());
    }
}