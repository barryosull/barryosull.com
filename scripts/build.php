<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Tests\Acceptance\Support\AppFactory;
use Tests\Acceptance\Support\AppHttp;

require __DIR__ . '/../bootstrap.php';

$assetDir = __DIR__ . "/../public";
$builtHtmlDir = __DIR__ . "/../public_html";
$fileSystem = new Filesystem(new Local($builtHtmlDir));
$webApp = AppFactory::make();

copyAssets($assetDir, $builtHtmlDir);

$urls = $webApp->getUrls();
foreach ($urls as $url) {
    buildStaticPage($webApp, $fileSystem, $url);
}

function copyAssets($assetDir, $builtHtmlDir)
{
    exec("cp -Rf $assetDir/ $builtHtmlDir");
    exec("rm $builtHtmlDir/index.php");
}

function buildStaticPage(AppHttp $webApp, Filesystem $fileSystem, string $url): void
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

