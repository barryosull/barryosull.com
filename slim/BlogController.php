<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BlogController
{
    const PER_PAGE = 8;

    public function handle(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contentRepository = new ContentRepository();
        $renderer = new Renderer();

        $page = $this->getPage($args);
        $category = $args['category'] ?? null;

        $articles = $contentRepository->fetchCollection($category);
        $categories = $contentRepository->fetchAllCategories();

        $urlPrevPage = $this->getUrlForPrevPage($page);
        $urlNextPage = $this->getUrlForNextPage($articles, $page, self::PER_PAGE);

        $body = $renderer->render("blog", [
            'articles' => array_slice($articles, $page * self::PER_PAGE, self::PER_PAGE),
            'categories' => $categories,
            'urlPrevPage' => $urlPrevPage,
            'urlNextPage' => $urlNextPage
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    public static function getPageCount(array $articles): int
    {
        return intval(ceil(count($articles)/self::PER_PAGE));
    }

    /**
     * @param array $args
     * @return int
     */
    private function getPage(array $args): int
    {
        return isset($args['page']) ? intval($args['page']) : 0;
    }

    /**
     * @param $page
     * @return null|string
     */
    private function getUrlForPrevPage($page)
    {
        $urlPrevPage = null;
        if ($page > 1) {
            $urlPrevPage = "/blog/page-" . ($page - 1);
        }
        if ($page == 1) {
            $urlPrevPage = "/blog";
        }
        return $urlPrevPage;
    }

    /**
     * @param $articles
     * @param $page
     * @param $perPage
     * @return null|string
     */
    private function getUrlForNextPage($articles, $page, $perPage)
    {
        $urlNextPage = null;
        if (count($articles) > (($page + 1) * $perPage)) {
            $urlNextPage = "/blog/page-" . ($page + 1);
        }
        return $urlNextPage;
    }
}
