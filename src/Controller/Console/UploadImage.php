<?php namespace Nekudo\ShinyBlog\Controller\Console;

use Ramsey\Uuid;
use Nekudo\ShinyBlog\Usecase;

class UploadImage
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

        $usecase = new Usecase\UploadImage();

        $url = $usecase->run($local_file_path, $image_id);

        exec("printf $url | pbcopy");

        echo "\nURL: $url\n";
    }

    private function getId($argv)
    {
        return isset($argv[2]) ? $argv[2] : Uuid\Uuid::uuid4()->toString();
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