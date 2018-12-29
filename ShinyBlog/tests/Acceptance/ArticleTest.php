<?php namespace Tests\Acceptance;

use GuzzleHttp\Psr7\Response;
use Nekudo\ShinyBlog\ShinyBlog;
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
        $response = $this->visitUrl($articleUri);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertContains(">barryosull.com</a>", strval($response->getBody()));
    }

    public function getAllArticleUrls()
    {
        $url = '/blog/feed';

        $response = $this->visitUrl($url);
        $xmlString = $response->getBody();

        $xml = new SimpleXMLElement($xmlString);

        $urls = [];
        foreach($xml->channel->item as $item) {
            $urls[] = [
                str_replace('http://localhost', '', strval($item->link))
            ];
        }

        return $urls;
    }

    private function visitUrl(string $url): Response
    {
        $_SERVER['REQUEST_URI'] = $url;

        $this->getBlog()->run();

        $shinyResponse = $this->getBlog()->run();

        return new Response(
            $shinyResponse->getStatusCode(),
            [],
            $shinyResponse->getBody()
        );
    }

    private static $blog;

    private function getBlog(): ShinyBlog
    {

        if (is_null(self::$blog)) {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['HTTP_HOST'] = 'localhost';

            $config = require __DIR__.'/../../src/config.php';
            self::$blog = new ShinyBlog($config);
        }

        return self::$blog;
    }
}