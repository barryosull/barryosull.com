<?php

require __DIR__ . '/vendor/autoload.php';

function CopyImageOverToServer($host, $image): string
{
    $image_id = Ramsey\Uuid\Uuid::uuid4();

    $server_image_path = "images/".$image_id->toString().".".imageType($image);

    $command = "scp $image $host:/var/www/barryosull.com/public/$server_image_path";

    exec($command);

    return "http://barryosull.com/$server_image_path";
}

function ImageType(string $path): string
{
    $parts = explode(".", $path);
    return last($parts);
}

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$host = getenv('REMOTE_HOST');

if (!isset($argv[1])) {
    echo "No file give, exiting.";
}

$filepath = $argv[1];

$url = CopyImageOverToServer($host, $filepath);

echo "\nURL: $url\n";

