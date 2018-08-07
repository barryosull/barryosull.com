<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArticleTest extends TestCase
{
    /**
     * @test
     */
    public function load_an_article()
    {
        $slug = 'why-i-don-t-like-traits';
        $response = $this->get("/api/article/$slug");

        $response->assertStatus(200);

        $article = json_decode($response->getContent());

        $this->assertPropertyExists([
            'title',
            'description',
            'slug',
            'date',
            'author',
            'categories',
            'published',
            'content',
            'excerpt',
            'coverImage'
        ], $article);
    }

    /**
     * @test
     */
    public function load_a_page_of_articles()
    {
        $response = $this->get("/api/article");

        $response->assertStatus(200);

        $json = json_decode($response->getContent());

        foreach ($json as $article) {
            $this->assertPropertyExists([
                'title',
                'description',
                'slug',
                'date',
                'author',
                'categories',
                'published',
                'content',
                'excerpt',
                'coverImage'
            ], $article);
        }
    }

    private function assertPropertyExists(array $keys, $object)
    {
        
        foreach ($keys as $key) {
            $this->assertTrue(property_exists($object, $key), "Could not find '$key' in object '".json_encode($object)."'");
        }
    }

}
