<?php

require __DIR__ . '/../src/bootstrap.php';

$app = \Nekudo\ShinyBlog\AppFactory::make($_SERVER['REQUEST_URI']);

$app->run();



