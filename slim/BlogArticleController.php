<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BlogArticleController
{
    public function handle(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contentRepository = new ContentRepository();
        $renderer = new Renderer();

        $article = $contentRepository->fetchArticleBySlug($args['slug']);

        $body = $renderer->render("article", [
            'page' => (object)$article,
            'article' => $article,
            "uri" => strval($request->getUri())
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
