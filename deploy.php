<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$host = getenv('REMOTE_HOST');

exec('ssh '.$host.' "cd /var/www/barryosull.com/ && git pull origin master && composer update"');