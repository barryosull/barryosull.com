<?php

require __DIR__ . '/../bootstrap.php';

if (isSlimAppRequest($_SERVER['REQUEST_URI'])) {

    $slimApp = new \Barryosull\Slim\App();
    $slimApp->run();
}

$shinyBlogApp = \Nekudo\ShinyBlog\AppFactory::make($_SERVER['REQUEST_URI']);
$shinyBlogApp->run();

function isSlimAppRequest(string $uri): bool
{
    if ($uri == "/") {
        return true;
    }
    if (isBlogRequest($uri) && (!isBlogFeedRequest($uri))) {
        return true;
    }
    return false;
}

function isBlogRequest($uri): bool
{
    return strpos($uri, "/blog") !== false;
}

function isBlogFeedRequest($uri): bool
{
    return $uri == "/blog/feed";
}



