<?php

$assetDir = __DIR__ . "/../public";
$builtHtmlDir = __DIR__ . "/../public_html";
copyAssets($assetDir, $builtHtmlDir);

use Barryosull\Slim\WebApp;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

require __DIR__ . '/../bootstrap.php';

$fileSystem = new Filesystem(new Local($builtHtmlDir));
$app = new WebApp();

$urls = (new \Barryosull\Slim\UrlService())->getAvailableUrls();

foreach ($urls as $url) {
    buildStaticPage($app, $fileSystem, $url);
}

function copyAssets($assetDir, $builtHtmlDir)
{
    exec("cp .env.example .env");
    exec("cp -Rf $assetDir/ $builtHtmlDir");
    exec("rm $builtHtmlDir/index.php");
}

function buildStaticPage(WebApp $webApp, Filesystem $fileSystem, string $url): void
{
    $response = $webApp->visitUrl($url);

    if ($response->getStatusCode() !== 200) {
        $code = $response->getStatusCode();
        throw new \Exception("Error building site, URL '$url' returned a '$code' status code");
    }

    $filePath = $url . "/index.html";
    $fileSystem->put($filePath, strval($response->getBody()));

    echo "Static page created for url:'$url'' at path:'$filePath'\n";
}

