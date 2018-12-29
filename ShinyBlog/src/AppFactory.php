<?php

namespace Nekudo\ShinyBlog;

class AppFactory
{
    public static function make($requestUri): App
    {
        return self::makeBlog();
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
}