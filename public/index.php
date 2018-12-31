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
    if (isBlogRequest($uri) && (!isBlogPagination($uri) && !isBlogFeedRequest($uri) && !isBlogCategoryRequest($uri))) {
        return true;
    }
    return false;
}

function isBlogRequest($uri): bool
{
    return strpos($uri, "/blog/") !== false;
}

function isBlogPagination($uri): bool
{
    return strpos($uri, "/blog/page-") !== false;
}

function isBlogFeedRequest($uri): bool
{
    return $uri == "/blog/feed";
}

function isBlogCategoryRequest($uri): bool
{
    return strpos($uri, "/blog/category") !== false;
}



