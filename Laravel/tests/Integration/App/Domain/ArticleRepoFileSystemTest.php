<?php
declare(strict_types=1);

namespace Tests\Integration\App\Domain;

use App\Domain\ArticleRepo;
use App\Domain\ArticleRepoFileSystem;

class ArticleRepoFileSystemTest extends ArticleRepoTest
{
    protected function makeRepo(): ArticleRepo
    {
        $testArticlePath = '/tmp/articles/';
        if (is_dir($testArticlePath)) {
            $this->emptyFolder($testArticlePath);
            rmdir($testArticlePath);
        }
        mkdir($testArticlePath);
        return new ArticleRepoFileSystem($testArticlePath);
    }

    private function emptyFolder(string $dir)
    {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                unlink($dir."/".$object);
            }
        }
    }
}
