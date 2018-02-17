<?php namespace Tests\Acceptance;

class UploadImageTest extends \PHPUnit\Framework\TestCase
{
    const SCRIPT_DIR = __DIR__ . "/../../scripts";

    function test_can_upload_local_image()
    {
        $test_image = __DIR__ . "/../assets/image.jpg";

        $last_line = system("php ".self::SCRIPT_DIR."/upload-image.php $test_image image-upload-test");

        $url = str_replace("URL: ", "", $last_line);

        $this->assertTrue(
            file_get_contents($url) !== false,
            "UploadLocalImage: Cannot read image '$url', inaccessible"
        );
    }

    function test_can_upload_remote_image()
    {
        $test_image = "http://barryosull.com/images/image-upload-test.jpg";

        $last_line = system("php ".self::SCRIPT_DIR."/upload-image.php $test_image image-remote-test");

        $url = str_replace("URL: ", "", $last_line);

        $this->assertTrue(
            file_get_contents($url) !== false,
            "TestCanUploadRemoteImage: Cannot read image '$url', inaccessible"
        );
    }
}
