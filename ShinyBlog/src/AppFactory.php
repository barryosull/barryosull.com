<?php

namespace Nekudo\ShinyBlog;

const ANNOTATION_API_URI = "/api/annotator";

class AppFactory
{
    public static function make($requestUri): App
    {
        if (self::isAnnotationsApiCall($requestUri)) {
            return self::makeAnnotationApiApp();
        } else {
            return self::makeBlog();
        }
    }

    private static function isAnnotationsApiCall(string $requestUri)
    {
        return strpos($requestUri, ANNOTATION_API_URI) === 0;
    }

    private static function makeBlog(): App
    {
        return new class implements App
        {
            public function run()
            {
                $config = require __DIR__.'/../src/config.php';
                $blog = new ShinyBlog($config);
                $blog->run()->respond();
            }
        };
    }

    private static function makeAnnotationApiApp()
    {
        return new class implements App
        {
            public function run()
            {
                $annotationApi = new \Annotator\Flysystem\App(
                    ANNOTATION_API_URI,
                    __DIR__."/../storage/annotations"
                );
                $annotationApi->run();
            }
        };
    }
}