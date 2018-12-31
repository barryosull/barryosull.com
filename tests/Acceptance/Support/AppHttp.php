<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

use Psr\Http\Message\ResponseInterface;

class AppHttp implements App
{
    public function visitUrl(string $url): ResponseInterface
    {
        $app = new HttpServer;
        $app->boot();

        $client = HttpServer::makeClient();

        return $client->get($url);
    }
}
