<?php

require __DIR__ . '/../ShinyBlog/src/bootstrap.php';

if (isSlimAppRequest($_SERVER['REQUEST_URI'])) {

    $slimApp = new \Slim\App();
    $slimApp->get('/', function ($request, $response, $args) {
        return $response->getBody()->write("Hello, " . $args['name']);
    });

    $slimApp->run();
    return;
}

$shinyBlogApp = \Nekudo\ShinyBlog\AppFactory::make($_SERVER['REQUEST_URI']);
$shinyBlogApp->run();

function isSlimAppRequest(string $uri)
{
    return false;
    if ($uri == "/") {
        return true;
    }
}



