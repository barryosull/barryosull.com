<?php
declare(strict_types=1);

namespace Tests\Integration\App\Domain;

use App\Domain\ArticleRepo;
use App\Domain\ArticleRepoFileSystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class ArticleRepoFileSystemTest extends ArticleRepoTest
{
    protected function makeEmptyRepo(): ArticleRepo
    {
        $testArticlePath = '/tmp/articles/';
        $adapter = new Local($testArticlePath);
        $filesystem = new Filesystem($adapter);

        foreach ($filesystem->listContents() as $file) {
            $filesystem->delete($file['basename']);
        }

        return new ArticleRepoFileSystem($filesystem);
    }
}
