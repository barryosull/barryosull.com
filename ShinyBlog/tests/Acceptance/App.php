<?php
declare(strict_types=1);

namespace Tests\Acceptance;

use GuzzleHttp\Psr7\Response;
use Nekudo\ShinyBlog\ShinyBlog;

class App
{
    public function visitUrl(string $url): Response
    {
        $_SERVER['REQUEST_URI'] = $url;

        $this->getBlog()->run();

        $shinyResponse = $this->getBlog()->run();

        return new Response(
            $shinyResponse->getStatusCode(),
            [],
            $shinyResponse->getBody()
        );
    }

    private static $blog;

    private function getBlog(): ShinyBlog
    {
        if (is_null(self::$blog)) {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['HTTP_HOST'] = 'localhost';

            $config = require __DIR__.'/../../src/config.php';
            self::$blog = new ShinyBlog($config);
        }

        return self::$blog;
    }
}
