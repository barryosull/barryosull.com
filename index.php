<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

require __DIR__.'/src/bootstrap.php';
