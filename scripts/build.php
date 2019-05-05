<?php

require __DIR__ . '/../bootstrap.php';

$url = "/";

$webApp = \Tests\Acceptance\Support\AppFactory::make();

buildStaticPage($webApp, $url);

/**
 * @param $webApp
 * @param $url
 * @throws Exception
 */
function buildStaticPage($webApp, $url): void
{
    $response = $webApp->visitUrl($url);

    if ($response->getStatusCode() !== 200) {
        $code = $response->getStatusCode();
        throw new \Exception("Error building site, URL '$url' returned a '$code' status code");
    }

    $publicDir = __DIR__ . "/../public/";
    $filePath = $publicDir . $url . "/index.html";
    file_put_contents($filePath, strval($response->getBody()));

    echo "Static page created for url:'$url'' at path:'$filePath'\n";
}

