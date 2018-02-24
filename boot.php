<?php

require_once __DIR__."/vendor/autoload.php";

use Nekudo\ShinyBlog\Services\FileCache;

(new FileCache())->clear();

echo "FileCache cleared\n";
return 0;

