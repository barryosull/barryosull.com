<?php
/**
 * ShinyBlog
 *
 * by Simon Samtleben <simon@nekudo.com>
 *
 * A simple and clean Blog/CMS application.
 * For more information visit: https://github.com/nekudo/shiny_blog
 */

declare(strict_types=1);
namespace Nekudo\ShinyBlog;

use Exception;
use Nekudo\ShinyBlog\Responder\Responder;
use RuntimeException;
use FastRoute;
use FastRoute\RouteCollector;
use Nekudo\ShinyBlog\Responder\HttpResponder;
use Nekudo\ShinyBlog\Responder\NotFoundResponder;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ShinyBlog
{
    /** @var array $config */
    protected $config;

    /** @var FastRoute\Dispatcher $dispatcher */
    protected $dispatcher;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * ShinyBlog main method.
     * Dispatches and handles requests.
     * @return HttpResponder
     */
    public function run(): HttpResponder
    {
        try {
            $this->setRoutes();
            $httpMethod = $_SERVER['REQUEST_METHOD'];
            $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            $response =  $this->dispatch($httpMethod, $uri);
        } catch (Exception $e) {
            $response = new HttpResponder($this->config);
            $response->error($e->getMessage());
        }

        return $response;
    }

    /**
     * Defines blog and page routes.
     *
     * @throws RuntimeException
     */
    protected function setRoutes()
    {
        if (empty($this->config['routes'])) {
            throw new RuntimeException('No routes defined in configuration file.');
        }
        $this->dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $collector) {
            foreach ($this->config['routes'] as $routeName => $route) {
                if (empty($route['method']) || empty($route['route']) || empty($route['action'])) {
                    throw new RuntimeException('Invalid route in configuration.');
                }
                $collector->addRoute($route['method'], $route['route'], $route['action']);
            }
        });
    }

    // Not currently used
    // TODO: Figure out why cache clearning isn't working
    protected function dispatchWithCache(string $httpMethod, string $uri): HttpResponder
    {
        $cache = new FilesystemCache('http.response');

        $key = "$httpMethod ".str_replace("/", ".", $uri);

        if ($cache->has($key)) {
            $serialised = $cache->get($key);
            return unserialize($serialised);
        }

        $response = $this->dispatch($httpMethod, $uri);

        $cache->set($key, serialize($response));

        return $response;
    }

    protected function dispatch(string $httpMethod, string $uri): HttpResponder
    {
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        if (!isset($routeInfo[0])) {
            throw new RuntimeException('Could not dispatch request.');
        }
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $responder = new NotFoundResponder($this->config);
                $response = $responder->__invoke();
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $responder = new HttpResponder($this->config);
                $response = $responder->methodNotAllowed();
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $arguments = $routeInfo[2];
                $response = $this->runAction($handler, $arguments);
                break;
            default:
                throw new RuntimeException('Could not dispatch request.');
        }
        return $response;
    }

    /**
     * Calls an action.
     *
     * @param string $actionName
     * @param array $arguments
     * @return Responder
     */
    protected function runAction(string $actionName, array $arguments = []): HttpResponder
    {
        $actionNamespace = "\\Nekudo\\ShinyBlog\\Controller\\Http";
        $actionController = "Show".ucfirst($actionName);

        $actionClassPath = $actionNamespace."\\".$actionController;
        
        if (!class_exists($actionClassPath)) {
            throw new RuntimeException('Invalid action.');
        }

        $action = new $actionClassPath($this->config);
        return $action->__invoke($arguments);
    }
}
