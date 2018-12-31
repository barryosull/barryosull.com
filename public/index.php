<?php

require __DIR__ . '/../ShinyBlog/src/bootstrap.php';

if (isSlimAppRequest($_SERVER['REQUEST_URI'])) {

    $slimApp = new \Barryosull\Slim\App();
    $slimApp->run();
}

$shinyBlogApp = \Nekudo\ShinyBlog\AppFactory::make($_SERVER['REQUEST_URI']);
$shinyBlogApp->run();

function isSlimAppRequest(string $uri)
{
    if ($uri == "/") {
        return true;
    }
}



