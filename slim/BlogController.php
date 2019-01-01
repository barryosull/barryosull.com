<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class BlogController
{
    public function handle($request, $response, $args)
    {
        $contentRepository = new ContentRepository();
        $renderer = new Renderer();

        $page = isset($args['page']) ? intval($args['page']) : 0;
        $category = $args['category'] ?? null;

        $articles = $contentRepository->fetchCollection($category);
        $categories = $contentRepository->fetchAllCategories();

        $urlPrevPage = null;
        $urlNextPage = null;

        if ($page > 1) {
            $urlPrevPage = "/blog/page-" . ($page-1);
        }
        if ($page == 1) {
            $urlPrevPage = "/blog";
        }

        $perPage = 8;

        if (count($articles) > (($page+1) * $perPage)) {
            $urlNextPage = "/blog/page-" . ($page+1);
        }

        echo $renderer->render("blog", [
            'articles' => array_slice($articles, $page * $perPage, 8),
            'categories' => $categories,
            'urlPrevPage' => $urlPrevPage,
            'urlNextPage' => $urlNextPage
        ]);
    }
}
