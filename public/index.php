<?php

require __DIR__ . '/../src/bootstrap.php';

const ANNOTATION_API_URI = "/api/annotator";

function isAnnotationsApiCall()
{
    return strpos($_SERVER['REQUEST_URI'], ANNOTATION_API_URI) === 0;
}

function bootBlog()
{
    $config = require __DIR__.'/../src/config.php';
    $blog = new Nekudo\ShinyBlog\ShinyBlog($config);
    $blog->run()->respond();
}

function bootAnnotationAPI()
{
    $annotationApi = new \Annotator\Flysystem\App(
        ANNOTATION_API_URI,
        __DIR__."/../storage/annotations"
    );
    $annotationApi->boot();
    return;
}

if (isAnnotationsApiCall()) {
    bootAnnotationAPI();
} else {
    bootBlog();
}



