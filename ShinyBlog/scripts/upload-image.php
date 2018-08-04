<?php

require __DIR__ . '/../src/bootstrap.php';

(new \Nekudo\ShinyBlog\Controller\Console\UploadImage())->exec($argv);



