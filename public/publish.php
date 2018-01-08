<?php

$password = 'release-the-kraken';

if ($_GET['command'] != $password) {
    header("HTTP/1.0 404 Not Found", true, 404);
    exit;
}

chdir("../");
exec("git pull origin master");