<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class App
{
    public function run()
    {
        $slimApp = $this->makeApp();

        $slimApp->get('/', function ($request, $response, $args) {

            $pageRepo = new PageRepository();
            $renderer = new Renderer();

            $page = $pageRepo->fetchPage('home');

            $renderer->render("page", ['page'=>(object)$page]);
        });

        $slimApp->get("/blog/{slug}", function ($request, $response, $args) {

            $pageRepo = new PageRepository();
            $renderer = new Renderer();

            $article = $pageRepo->fetchArticleBySlug($args['slug']);

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
