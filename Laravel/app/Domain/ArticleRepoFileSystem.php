<?php
declare(strict_types=1);

namespace App\Domain;

use Cocur\Slugify\Slugify;
use Symfony\Component\Yaml\Yaml;

class ArticleRepoFileSystem implements ArticleRepo
{
    private $articlePath;

    public function __construct(string $articlePath = null)
    {
        $this->articlePath = $articlePath ?? app_path('../../contents/articles/');
    }

    public function find(string $slug): Article
    {
        $pageFilenames = array_diff(scandir($this->articlePath), array('..', '.'));

        foreach ($pageFilenames as $pageFilename) {
            $pathToArticle = $this->articlePath.$pageFilename;

            $page = $this->parseContentFile($pathToArticle);

            if ($page['slug'] == $slug) {
                return Article::fromArray($page);
            }
        }

        throw new \Exception('Page content not found');
    }

    /**
     * @param null|string $tag
     * @return Article[]
     */
    public function list(?string $tag = null): array
    {
        $pageFilenames = array_reverse(array_diff(scandir($this->articlePath), array('..', '.')));

        $articles = array_map(function ($pageFilename) {
            $pathToArticle = $this->articlePath.$pageFilename;
            return $this->parseContentFile($pathToArticle);
        }, $pageFilenames);

        if ($tag) {
            $articles = $this->filterByTag($articles, $tag);
        }

        $articles = array_values($articles);

        return array_map(function(array $article){
            return Article::fromArray($article);
        }, $articles);
    }

    private function filterByTag(array $articles, string $tag): array
    {
        return array_filter($articles, function($article) use ($tag) {
            $tags = explode(",", $article['tags'] ?? "");
            $tags = array_map(function ($articleTag) {
                return trim($articleTag);
            }, $tags);
            return in_array($tag, $tags);
        });
    }

    private function parseContentFile(string $pathToFile) : array
    {
        if (!file_exists($pathToFile)) {
            throw new \Exception('Page content not found');
        }
        $contentRaw = file_get_contents($pathToFile);
        if (empty($contentRaw)) {
            throw new \Exception('Invalid content file.');
        }

        if ($this->isJsonMeta($contentRaw)) {
            list($data, $content) = $this->parseJsonMeta($contentRaw);
        } else if ($this->isJekyllMeta($contentRaw)) {
            list($data, $content) = $this->parseJekyllMeta($contentRaw, $pathToFile);
        } else {
            throw new \Exception('Invalid content file, missing meta information');
        }

        $data['content'] = $content;
        return $data;
    }

    private function parseJsonMeta(string $contentRaw): array
    {
        $sections = explode('::METAEND::', $contentRaw);
        $data = json_decode($sections[0], true);
        $content = trim($sections[1]);

        return [$data, $content];
    }

    // TODO: test parsing when info is missing (ideally put logic in article where it belongs)
    private function parseJekyllMeta(string $contentRaw, string $pathToFile): array
    {
        $sections = explode('---', $contentRaw);
        $data = Yaml::parse($sections[1], Yaml::PARSE_DATETIME);
        if (!isset($data['slug'])) {
            $data['slug'] = (new Slugify())->slugify($data['title']);
        }
        if (!isset($data['author'])) {
            $data['author'] = 'Barry';
        }
        if (isset($data['date'])) {
            $data['date'] = $data['date']->format('Y-m-d');
        } else {
            $file_name = last(explode("/", $pathToFile));
            $data['date'] = substr($file_name, 0, 10);
        }
        $data['categories'] = $data['tags'] ? explode(",", $data['tags']) : [];
        $data['coverImage'] = $data['cover_image'] ?? null;

        $content = trim(
            implode("---",
                array_values(
                    array_slice($sections, 2)
                )
            )
        );

        return [$data, $content];
    }

    private function isJsonMeta(string $contentRaw)
    {
        return strpos($contentRaw, '::METAEND::') !== false;
    }

    private function isJekyllMeta($contentRaw)
    {
        $sections = explode('---', $contentRaw);
        return count($sections) > 1;
    }

    public function store(Article $article)
    {
        $data = $article->toArray();

        $header = $this->makeHeader($data);

        $content = $header.$data['content'];

        $articlePath = $this->articlePath.$data['date'].'-'.$data['slug'].'.md';

        file_put_contents($articlePath, $content);
    }

    private function makeHeader(array $data)
    {
        return "---
title: {$data['title']}
published: ".($data['published'] ? 'true' : 'false')."
description: {$data['description']}
author: {$data['author']}
slug: {$data['slug']}
tags: ".implode(",", $data['categories'])."
cover_image: {$data['coverImage']}
---";
    }
}
