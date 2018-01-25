<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

(new \Nekudo\ShinyBlog\Controller\Console\UploadImage())->exec($argv);



