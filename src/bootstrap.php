<?php
declare(strict_types=1);

namespace Nekudo\ShinyBlog;

use Dotenv\Dotenv;

$dotenv = new Dotenv(__DIR__.'/..');
$dotenv->load();

$config = require 'config.php';
$blog = new ShinyBlog($config);
$blog->run();
