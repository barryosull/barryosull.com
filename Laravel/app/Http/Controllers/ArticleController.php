<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use ParsedownExtra;

class ArticleController
{
    private $articleRepo;
    private $parsedown;

    const EXERT_LENGTH = 640;

    public function __construct(ArticleRepo $articleRepo, ParsedownExtra $parsedown)
    {
        $this->articleRepo = $articleRepo;
        $this->parsedown = $parsedown;
    }

    public function get($articleSlug)
    {
        $article = $this->articleRepo->find($articleSlug);

        $article = $this->formatResponse($article);

        return response()->json($article);
    }

    private function formatResponse($article): array
    {
        $article['content'] = $this->parsedown->parse($article['content']);
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

    public function list()
    {
        $articles = $this->articleRepo->list();

        $articles = array_map(function($article){
            return $this->formatResponse($article);
        }, $articles);

        return response()->json($articles);
    }
}
