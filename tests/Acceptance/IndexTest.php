<?php namespace Tests\Acceptance;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class IndexTest extends TestCase
{
    public function test_load_homepage()
    {
        $client = new Client();
        $homepage = getenv('DOMAIN');
        $response = $client->get($homepage);

        $this->assertEquals(200, $response->getStatusCode());
    }
}