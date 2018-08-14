<?php
declare(strict_types=1);

namespace Tests\Support;

use App\Domain\Article;
use App\Domain\Categories;

class ArticleFactory
{
    public static function makeArticle(string $slug, string $tag='tag', $date='2010-01-01'): Article
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
