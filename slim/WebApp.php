<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class WebApp
{
    public function run()
    {
        $slimApp = $this->makeApp();

        $slimApp->get('/', function ($request, $response, $args) {
            (new HomeController())->handle($request, $response, $args);
        });

        $slimApp->get('/blog[/page-{page}]', function ($request, $response, $args) {
            (new BlogController())->handle($request, $response, $args);
        });

        $slimApp->get('/blog/category/{category}[/page-{page}]', function ($request, $response, $args) {
            (new BlogController())->handle($request, $response, $args);
        });

        $slimApp->get("/blog/feed", function ($request, $response, $args) {
            (new BlogFeedController())->handle($request, $response, $args);
        });

        $slimApp->get("/blog/{slug}", function ($request, $response, $args) {
            (new BlogArticleController())->handle($request, $response, $args);
        });

        $slimApp->run();
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
