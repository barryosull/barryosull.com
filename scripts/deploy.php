<?php

require __DIR__ . '/../bootstrap.php';

$host = getenv('REMOTE_HOST');

`ssh $host "cd /var/www/barryosull.com/ && git pull origin master && composer install"`;