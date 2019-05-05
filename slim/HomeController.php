<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController
{
    public function handle(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contentRepository = new ContentRepository();
        $renderer = new Renderer();

        $page = $contentRepository->fetchPage('home');

        $body = $renderer->render("page", ['page'=>$page, "uri" => strval($request->getUri())]);

        $response->getBody()->write($body);

        return $response;
    }
}
