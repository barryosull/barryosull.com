<?php

require __DIR__ . '/../ShinyBlog/src/bootstrap.php';

/*
$slimApp = new \Slim\App();
$slimApp->get('/hello/{name}', function ($request, $response, $args) {
    return $response->getBody()->write("Hello, " . $args['name']);
});
$slimApp->run();
*/

$shinyBlogApp = \Nekudo\ShinyBlog\AppFactory::make($_SERVER['REQUEST_URI']);
$shinyBlogApp->run();



