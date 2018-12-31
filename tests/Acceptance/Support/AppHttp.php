<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

use Psr\Http\Message\ResponseInterface;

class AppHttp
{
    public function visitUrl(string $url): ResponseInterface
    {
        $app = new HttpServer;
        $app->boot();

        $client = HttpServer::makeClient();

        $response = $client->get($url);

        if ($response->getStatusCode() != 200) {
            $filename = strtolower(str_replace("/", "-", $url));
            $dir = "/tmp/artifacts";
            $path = $dir . "/" . $filename;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            file_put_contents($path, strval($response->getBody()));
        }

        return $response;
    }
}
