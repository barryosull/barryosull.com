<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class App
{
    public function run()
    {
        $slimApp = $this->makeApp();

        $slimApp->get('/', function ($request, $response, $args) {

            $contentRepository = new ContentRepository();
            $renderer = new Renderer();

            $page = $contentRepository->fetchPage('home');

            $renderer->render("page", ['page'=>(object)$page]);
        });

        $slimApp->get('/blog[/page-{page}]', function ($request, $response, $args) {

            $contentRepository = new ContentRepository();
            $renderer = new Renderer();

            $page = isset($args['page']) ? intval($args['page']) : 0;

            $articles = $contentRepository->fetchCollection();
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

            $renderer->render("blog", [
                'articles' => array_slice($articles, $page * $perPage, 8),
                'categories' => $categories,
                'urlPrevPage' => $urlPrevPage,
                'urlNextPage' => $urlNextPage
            ]);
        });

        $slimApp->get("/blog/{slug}", function ($request, $response, $args) {

            $contentRepository = new ContentRepository();
            $renderer = new Renderer();

            $article = $contentRepository->fetchArticleBySlug($args['slug']);

            $renderer->render("article", ['page' => (object)$article, 'article' => (object)$article]);
        });

        $slimApp->run();
        return;
    }

    private function makeApp(): \Slim\App
    {
        $configuration = [
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ];
        $c = new \Slim\Container($configuration);

        return new \Slim\App($c);
    }
}
