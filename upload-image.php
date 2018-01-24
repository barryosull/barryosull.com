<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

class UseCase
{
    public function run(string $image, string $image_id): string
    {
        $host = getenv('REMOTE_HOST');

        $public_image_path = "images/".$image_id.".".$this->imageExt($image);

        $server_image_path = "/var/www/barryosull.com/public/$public_image_path";

        $command = "scp -p $image $host:$server_image_path";
        exec($command);

        $command = "ssh $host \"chown www-data:www-data $server_image_path && chmod 644 $server_image_path\"";
        exec($command);

        return "http://barryosull.com/$public_image_path";
    }

    function imageExt(string $path): string
    {
        $parts = explode(".", $path);
        return last($parts);
    }
}

class Console
{
    public function exec($argv)
    {
        if (!isset($argv[1])) {
            echo "No file give, exiting.";
        }

        $image_ref = $argv[1];

        $image_id = $this->getId($argv);

        if ($this->isLocalFile($image_ref)) {
            $local_file_path = $image_ref;
        } else {
            $local_file_path = $this->copyToLocalFilesystem($image_ref);
        }

        $usecase = new UseCase();

        $url = $usecase->run($local_file_path, $image_id);

        echo "\nURL: $url\n";
    }

    private function getId($argv)
    {
        return isset($argv[2]) ? $argv[2] : Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    private function isLocalFile($image_ref)
    {
        return file_exists($image_ref);
    }

    private function copyToLocalFilesystem($image_url)
    {
        $local_file_path = __DIR__.'/inprogress-upload.jpg';
        file_put_contents($local_file_path, file_get_contents($image_url));
        return $local_file_path;
    }
}

(new Console())->exec($argv);



