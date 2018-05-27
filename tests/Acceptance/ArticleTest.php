<?php namespace Tests\Acceptance;

use Nekudo\ShinyBlog\ShinyBlog;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class ArticleTest extends TestCase
{
    private $blog;

    private function getBlog(): ShinyBlog
    {

        if (is_null($this->blog)) {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['HTTP_HOST'] = 'localhost';

            $config = require __DIR__.'/../../src/config.php';
            $this->blog = new ShinyBlog($config);
        }

        return $this->blog;
    }

    /**
     * @test
     * @dataProvider getAllArticleUrls
     */
    public function it_loads_an_article(string $articleUri)
    {
        $_SERVER['REQUEST_URI'] = $articleUri;

        $response = $this->getBlog()->run();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains("<a href=\"/\">barryosull.com</a>", $response->getBody());
    }

    public function getAllArticleUrls()
    {
        $_SERVER['REQUEST_URI'] = '/blog/feed';

        $response = $this->getBlog()->run();
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
}