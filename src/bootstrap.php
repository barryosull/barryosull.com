<?php
declare(strict_types=1);

namespace Nekudo\ShinyBlog;

$config = require 'config.php';
$blog = new ShinyBlog($config);
$blog->run();
