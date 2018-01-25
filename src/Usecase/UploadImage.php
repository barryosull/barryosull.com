<?php namespace Nekudo\ShinyBlog\Usecase;

class UploadImage
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