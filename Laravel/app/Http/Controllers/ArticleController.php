<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Article;
use App\Domain\ArticleRepoFileSystem;
use Illuminate\Http\Request;
use ParsedownExtra;

class ArticleController
{
    private $articleRepo;
    private $parsedown;

    const EXERT_LENGTH = 640;

    const PAGE_SIZE = 10;

    public function __construct(ArticleRepoFileSystem $articleRepo, ParsedownExtra $parsedown)
    {
        $this->articleRepo = $articleRepo;
        $this->parsedown = $parsedown;
    }

    public function get($articleSlug)
    {
        $article = $this->articleRepo->find($articleSlug);

        $articleResponse = $this->formatResponse($article->toArray());

        return response()->json($articleResponse);
    }

    private function formatResponse($article): array
    {
        $article['content'] = $this->parsedown->parse($article['content']);
        $article['url'] = "/api/article/".$article['slug'];
        $article['excerpt'] = $this->makeExcerpt($article['content']);
        return $article;
    }

    private function makeExcerpt(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        $moreMarkerPosition = strpos($content, '<!--more-->');
        if (empty($moreMarkerPosition)) {
            $moreMarkerPosition = self::EXERT_LENGTH;
        }
        return substr($content, 0, $moreMarkerPosition)."... ";
    }

    public function list(Request $request)
    {
        $page = $request->get('page', 1) - 1;

        $tag = $request->get('tag', null);

        $articles = $this->articleRepo->list($tag);

        $articles = array_slice($articles, $page*self::PAGE_SIZE, self::PAGE_SIZE);

        $articles = array_map(function(Article $article){
            return $this->formatResponse($article->toArray());
        }, $articles);

        return response()->json($articles);
    }
}
