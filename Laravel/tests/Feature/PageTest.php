<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageTest extends TestCase
{
    /**
     * @test
     */
    public function load_the_homepage()
    {
        $response = $this->get('/api/page/home');

        $response->assertStatus(200);

        $json = json_decode($response->getContent());

        $this->assertTrue(isset($json->title));
        $this->assertTrue(isset($json->description));
        $this->assertTrue(isset($json->date));
        $this->assertTrue(isset($json->content));
    }
}
