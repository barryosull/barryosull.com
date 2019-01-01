<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BlogFeedController
{
    public function handle(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contentRepository = new ContentRepository();
        $renderer = new Renderer();

        $articles = $contentRepository->fetchCollection();

        $pubDate = date(DATE_RSS, strtotime($articles[0]->date));

        $response = $response->withHeader('Content-type', 'text/xml');

        $xml = $renderer->render("rss", [
            'root' => getenv("DOMAIN"),
            'articles' => $articles,
            'pubDate' => $pubDate,
        ]);

        $body = $response->getBody();
        $body->write($xml);

        return $response;
    }
}
