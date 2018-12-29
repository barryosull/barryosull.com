<?php namespace Tests\Acceptance;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class ArticleTest extends TestCase
{
    /**
     * @test
     * @dataProvider getAllArticleUrls
     */
    public function it_loads_an_article(string $articleUri)
    {
        $app = AppFactory::make();

        $response = $app->visitUrl($articleUri);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertContains(">barryosull.com</a>", strval($response->getBody()));
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
}