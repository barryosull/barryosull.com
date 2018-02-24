<?php

require __DIR__ . '/../src/bootstrap.php';

$host = getenv('REMOTE_HOST');

`ssh $host "cd /var/www/barryosull.com/ && git pull origin master && composer install"`;