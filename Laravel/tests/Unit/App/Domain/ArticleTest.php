<?php
declare(strict_types=1);

namespace Tests\Unit\App\Domain;

use App\Domain\ValueException;
use App\Domain\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    private function makeArticleArray(): array
    {
        return [
            'title' => "title",
            'description' => "description",
            'slug' => "slug",
            'date' => "2018-01-01",
            'author' => "author",
            'categories' => ['one', 'two'],
            'published' => true,
            'content' => "content",
            'coverImage' => "http://google.com/logo.png",
        ];
    }

    /**
     * @test
     */
    public function can_convert_an_article_to_an_array()
    {
        $expectedArray = $this->makeArticleArray();

        $article = Article::fromArray($expectedArray);

        $actualArray = $article->toArray();

        $this->assertEquals($expectedArray, $actualArray);
    }

    /**
     * @test
     */
    public function will_not_accept_invalid_cover_image_url()
    {
        $array = $this->makeArticleArray();
        $array['coverImage'] = 'dfsfdsf';

        $this->expectException(ValueException::class);

        Article::fromArray($array);
    }

    /**
     * @test
     */
    public function will_accept_empty_cover_image_url()
    {
        $array = $this->makeArticleArray();
        $array['coverImage'] = null;

        $article = Article::fromArray($array);
        $this->assertInstanceOf(Article::class, $article);
    }

    /**
     * @test
     */
    public function will_not_accept_invalid_date()
    {
        $array = $this->makeArticleArray();
        $array['date'] = '1234';

        $this->expectException(ValueException::class);

        Article::fromArray($array);
    }
}
