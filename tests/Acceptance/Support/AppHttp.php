<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

use Barryosull\Slim\BlogController;
use Barryosull\Slim\ContentRepository;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class AppHttp
{
    public function visitUrl(string $url): ResponseInterface
    {
        $app = new HttpServer;
        $app->boot();

        $client = HttpServer::makeClient();

        $response = $client->get($url);

        return $response;
    }
}
