<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

use GuzzleHttp\Client;

class HttpServer
{
    const HOST = '127.0.0.1:8000';
    const ENTRY_POINT = 'public/';

    private static $localWebServerId = null;

    public function boot()
    {
        $this->startWebServer();
    }

    public function startWebServer()
    {
        if (isset(self::$localWebServerId)) {
            return;
        }
        $this->launchWebServer();
        $this->stopWebserverOnShutdown();
        $this->waitUntilWebServerAcceptsRequests();
    }

    private function launchWebServer()
    {
        $rootDir = __DIR__ . "/../../../";

        $command = sprintf(
            'php -S %s -t %s >/dev/null 2>&1 & echo $!',
            self::HOST,
            $rootDir . self::ENTRY_POINT
        );

        exec($command, $output, $returnVar);
    
        self::$localWebServerId = (int) $output[0];
    }

    private function waitUntilWebServerAcceptsRequests()
    {
        $waitForItCmd = 'bash '.__DIR__.'/wait-for-it.sh -t 5 -q '.self::HOST;

        system($waitForItCmd, $returnVar);
        if ($returnVar !== 0) {
            throw new \Exception(
                "Unable to find webserver. Please check your have the correct paths configured."
            );
        }
    }

    private function stopWebServerOnShutdown()
    {
        register_shutdown_function(function () {
            exec('kill '.self::$localWebServerId);
        });
    }

    public static function makeClient(): Client
    {
        return new Client([
            'base_uri' => 'http://'.self::HOST,
            'http_errors' => false,
        ]);
    }
}