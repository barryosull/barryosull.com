<?php

namespace App\Providers;

use App\Domain\ArticleRepo;
use App\Domain\ArticleRepoFileSystem;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ArticleRepo::class,
            function($app){
                $testArticlePath = app_path('../../contents/articles/');
                $adapter = new Local($testArticlePath);
                $filesystem = new Filesystem($adapter);
                return new ArticleRepoFileSystem($filesystem);
            }
        );
    }
}
