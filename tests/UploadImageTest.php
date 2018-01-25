<?php

const SCRIPT_DIR = __DIR__."/../scripts";

function TestCanUploadLocalImage()
{
    $test_image = __DIR__."/assets/image.jpg";

    $last_line = system("php ".SCRIPT_DIR."/upload-image.php $test_image image-upload-test");

    $url = str_replace("URL: ", "", $last_line);

    if (file_get_contents($url) === false) {
        throw new \Exception("UploadLocalImage: Cannot read image '$url', inaccessible");
    }

    echo "Image is URL readable\n";
}

function TestCanUploadRemoteImage()
{
    $test_image = "http://barryosull.com/images/image-upload-test.jpg";

    $last_line = system("php ".SCRIPT_DIR."/upload-image.php $test_image image-remote-test");

    $url = str_replace("URL: ", "", $last_line);

    if (file_get_contents($url) === false) {
        throw new \Exception("TestCanUploadRemoteImage: Cannot read image '$url', inaccessible");
    }

    echo "Image is URL readable\n";
}

TestCanUploadLocalImage();
TestCanUploadRemoteImage();
