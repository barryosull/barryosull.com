<?php

namespace App\Domain;

interface ArticleRepo
{
    public function store(Article $article);

    public function find(string $slug): Article;

    /**
     * @param null|string $tag
     * @return Article[]
     */
    public function list(?string $tag=null): array;
}