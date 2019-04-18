<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TalksController
{
    public function handle(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contentRepository = new ContentRepository();
        $renderer = new Renderer();

        $page = $contentRepository->fetchPage('talks');

        $body = $renderer->render("page", ['page'=>$page]);

        $response->getBody()->write($body);

        return $response;
    }
}
