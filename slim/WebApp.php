<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class WebApp
{
    public function run()
    {
        $slimApp = $this->makeApp();

        $this->addCacheMiddleware($slimApp);

        $this->addRoutes($slimApp);

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

    /**
     * @param $slimApp
     */
    private function addRoutes($slimApp): void
    {
        $slimApp->get('/', function ($request, $response, $args) {
            return (new HomeController())->handle($request, $response, $args);
        });

        $slimApp->get('/blog[/page-{page}]', function ($request, $response, $args) {
            return (new BlogController())->handle($request, $response, $args);
        });

        $slimApp->get('/blog/category/{category}[/page-{page}]', function ($request, $response, $args) {
            return (new BlogController())->handle($request, $response, $args);
        });

        $slimApp->get("/blog/feed", function ($request, $response, $args) {
            return (new BlogFeedController())->handle($request, $response, $args);
        });

        $slimApp->get("/blog/{slug}", function ($request, $response, $args) {
            return (new BlogArticleController())->handle($request, $response, $args);
        });
    }

    /**
     * @param $slimApp
     */
    private function addCacheMiddleware($slimApp): void
    {
        $slimApp->add(function ($request, $response, $next) {

            $response = $next($request, $response);

            $response = $response->withHeader('Cache-Control', 'max-age=600, public');

            return $response;
        });
    }
}
