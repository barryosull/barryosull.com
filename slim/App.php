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

            $page = $args['page'] ?? 0;

            $articles = $contentRepository->fetchCollection($page);
            $categories = $contentRepository->fetchAllCategories();

            $renderer->render("blog", ['articles' => $articles, 'categories' => $categories]);
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
