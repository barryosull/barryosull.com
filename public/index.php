<?php

ini_set('display_errors', "1");
ini_set('display_startup_errors', "1");
error_reporting(E_ALL);

require __DIR__ . '/../src/bootstrap.php';

$config = require __DIR__.'/../src/config.php';
$blog = new Nekudo\ShinyBlog\ShinyBlog($config);
$blog->run();
