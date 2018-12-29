<?php namespace Tests\Acceptance;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Tests\Acceptance\Support\AppFactory;

class BlogTest extends TestCase
{
    /**
     * @test
     * @dataProvider getAllArticleUrls
     */
    public function displays_article(string $articleUri)
    {
        $app = AppFactory::make();

        $response = $app->visitUrl($articleUri);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains(">barryosull.com</a>", strval($response->getBody()));
    }

    /**
     * @test
     */
    public function display_list_of_articles()
    {
        $app = AppFactory::make();

        $response = $app->visitUrl("/blog");

        $this->hasArticles($response);
    }

    /**
     * @test
     */
    public function paginate_though_articles()
    {
        $app = AppFactory::make();

        $response = $app->visitUrl("/blog/page-2");

        $this->hasArticles($response);
    }

    /**
     * @test
     */
    public function list_articles_by_tag()
    {
        $app = AppFactory::make();

        $response = $app->visitUrl("/blog/category/architecture");

        $this->hasArticles($response);
    }

    public function getAllArticleUrls()
    {
        $url = '/blog/feed';

        $app = AppFactory::make();

        $response = $app->visitUrl($url);

        $xmlString = $response->getBody();

        $xml = new SimpleXMLElement($xmlString);

        $urls = [];
        foreach ($xml->channel->item as $item) {
            $urls[] = [
                str_replace('http://localhost', '', strval($item->link))
            ];
        }

        return $urls;
    }

    /**
     * @param $response
     */
    private function hasArticles($response): void
    {
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertGreaterThan(0, substr_count($response->getBody(), "class=\"excerpt\""));
    }
}