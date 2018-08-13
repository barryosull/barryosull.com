<?php

namespace Tests\Integration\App\Domain;

use App\Domain\Article;
use App\Domain\ArticleRepo;
use App\Domain\Categories;
use PHPUnit\Framework\TestCase;

abstract class ArticleRepoTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_fetch_articles_by_slug()
    {
        $slug = 'article-slug';
        $article = $this->makeArticle($slug);

        $repo = $this->makeRepo();
        $repo->store($article);

        $actualArticle = $repo->find($slug);

        $this->assertEquals($article, $actualArticle);
    }

    /**
     * @test
     */
    public function it_can_fetch_all_articles()
    {
        $articleA = $this->makeArticle('a');
        $articleB = $this->makeArticle('b');
        $articleC = $this->makeArticle('c');
        $expectedArticles = [$articleC, $articleB, $articleA];

        $repo = $this->makeRepo();
        $repo->store($articleA);
        $repo->store($articleB);
        $repo->store($articleC);

        $actualArticles = $repo->list();

        $this->assertEquals($expectedArticles, $actualArticles);
    }

    /**
     * @test
     */
    public function it_can_fetch_articles_that_match_a_tag()
    {
        $tagA = 'tag-a';
        $tagB = 'tag-b';

        $articleA = $this->makeArticle('a', $tagA);
        $articleB = $this->makeArticle('b', $tagA);
        $articleC = $this->makeArticle('c', $tagB);
        $expectedArticles = [$articleB, $articleA];

        $repo = $this->makeRepo();
        $repo->store($articleA);
        $repo->store($articleB);
        $repo->store($articleC);

        $actualArticles = $repo->list($tagA);

        $this->assertEquals($expectedArticles, $actualArticles);
    }

    abstract protected function makeRepo(): ArticleRepo;

    private function makeArticle(string $slug, string $tag='tag'): Article
    {
        $categories = Categories::fromArray([$tag]);
        return new Article(
            "title",
            "Desc",
            $slug,
            '2010-01-01',
            'barry',
            $categories,
            true,
            "",
            ""
        );
    }
}
