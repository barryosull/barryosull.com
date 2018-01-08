<?php

$password = 'release-the-kraken';

if ($_GET['command'] != $password) {
    header("HTTP/1.0 404 Not Found", true, 404);
    return;
}

chdir(__DIR__."/../");

exec("git pull origin master", $output);

$result = implode("\n", $output);

file_put_contents(__DIR__."/../release.output", $result);

echo nl2br($result);