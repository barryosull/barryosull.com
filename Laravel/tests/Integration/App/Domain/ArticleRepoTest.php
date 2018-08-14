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

        $repo = $this->makeEmptyRepo();
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
        $expectedArticles = [$articleA, $articleB, $articleC];

        $repo = $this->makeEmptyRepo();
        $repo->store($articleA);
        $repo->store($articleB);
        $repo->store($articleC);

        $actualArticles = $repo->list();

        $this->assertEquals($expectedArticles, $actualArticles);
    }

    /**
     * @test
     */
    public function articles_are_ordered_by_date_desc()
    {
        $articleA = $this->makeArticle('a', 'tag', '2012-01-01');
        $articleB = $this->makeArticle('b', 'tag', '2014-01-01');
        $articleC = $this->makeArticle('c', 'tag', '2013-01-01');

        $expectedArticleOrder = [$articleB, $articleC, $articleA];

        $repo = $this->makeEmptyRepo();
        $repo->store($articleA);
        $repo->store($articleB);
        $repo->store($articleC);

        $actualArticles = $repo->list();

        $this->assertEquals($expectedArticleOrder, $actualArticles);
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
        $expectedArticles = [$articleA, $articleB];

        $repo = $this->makeEmptyRepo();
        $repo->store($articleA);
        $repo->store($articleB);
        $repo->store($articleC);

        $actualArticles = $repo->list($tagA);

        $this->assertEquals($expectedArticles, $actualArticles);
    }

    abstract protected function makeEmptyRepo(): ArticleRepo;

    private function makeArticle(string $slug, string $tag='tag', $date='2010-01-01'): Article
    {
        $categories = Categories::fromArray([$tag]);
        return new Article(
            "title",
            "Desc",
            $slug,
            $date,
            'barry',
            $categories,
            true,
            "",
            ""
        );
    }
}
