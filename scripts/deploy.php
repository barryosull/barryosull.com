<?php

require __DIR__ . '/../src/bootstrap.php';

$host = getenv('REMOTE_HOST');

exec('ssh '.$host.' "cd /var/www/barryosull.com/ && git pull origin master && composer update"');