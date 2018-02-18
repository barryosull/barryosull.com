<?php

use Symfony\Component\Cache\Simple\FilesystemCache;

require __DIR__ . '/../src/bootstrap.php';

$host = getenv('REMOTE_HOST');

exec('ssh '.$host.' "cd /var/www/barryosull.com/ && git pull origin master && composer install && php boot.php"');