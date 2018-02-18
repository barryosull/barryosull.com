<?php

require_once __DIR__."/vendor/autoload.php";

use Symfony\Component\Cache\Simple\FilesystemCache;

(new FilesystemCache())->clear();

echo "FileCache cleared\n";