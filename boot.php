<?php

require_once __DIR__."/vendor/autoload.php";

use Symfony\Component\Cache\Simple\FilesystemCache;

$is_cleared = (new FilesystemCache('http.response'))->clear();

if ($is_cleared) {
    echo "FileCache cleared\n";
    return 0;
}

echo "Error: FileCache clearing failed, please check log";
return 1;

