<?php

namespace Tests\Feature;

use App\Http\Controllers\ArticleController;
use Tests\TestCase;

/**
 * TODO: Make this test not brittle, seed the repo with data rather than relying on existing content
 */
class ArticleTest extends TestCase
{
    private $repo;

    public function setupAdapters()
    {

    }

    /**
     * @test
     */
    public function load_an_article()
    {
        $slug = 'why-i-don-t-like-traits';
        $response = $this->get("/api/article/$slug");

        $response->assertStatus(200);

        $article = json_decode($response->getContent());

        $this->assertHasArticleSchema($article);
    }

    private function assertHasArticleSchema($article)
    {
        $this->assertPropertiesExists([
            'title',
            'description',
            'slug',
            'date',
            'author',
            'categories',
            'published',
            'content',
            'excerpt',
            'coverImage',
            'url'
        ], $article);
    }

    private function assertArticlesIsPublished($article)
    {
        return $article->published == true;
    }

    /**
     * @test
     */
    public function load_a_page_of_articles()
    {
        $response = $this->get("/api/article");

        $response->assertStatus(200);

        $json = json_decode($response->getContent());

        $this->assertCount(ArticleController::PAGE_SIZE, $json);

        foreach ($json as $article) {
            $this->assertHasArticleSchema($article);
            $this->assertArticlesIsPublished($article);
        }
    }

    /**
     * @test
     */
    public function paginate_through_article_list()
    {
        $response = $this->get("/api/article?page=2");

        $response->assertStatus(200);

        $json = json_decode($response->getContent());

        $this->assertCount(7, $json);
    }

    /**
     * @test
     */
    public function filter_by_tag()
    {
        $response = $this->get("/api/article?tag=book");

        $response->assertStatus(200);

        $json = json_decode($response->getContent());

        $this->assertCount(1, $json);
    }

    /**
     * @test
     */
    public function fetch_unpublished()
    {
        $response = $this->get("/api/article?unpublished=1");

        $response->assertStatus(200);

        $json = json_decode($response->getContent());

        $this->assertCount(9, $json);
    }

    private function assertPropertiesExists(array $keys, $object)
    {
        foreach ($keys as $key) {
            $this->assertTrue(property_exists($object, $key), "Could not find '$key' in object '".json_encode($object)."'");
        }
    }

}
