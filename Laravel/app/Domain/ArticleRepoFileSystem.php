<?php
declare(strict_types=1);

namespace App\Domain;

use Cocur\Slugify\Slugify;
use DateTime;
use Symfony\Component\Yaml\Yaml;
use League\Flysystem\Filesystem;

class ArticleRepoFileSystem implements ArticleRepo
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function find(string $slug): Article
    {
        $contents = $contents = $this->filesystem->listContents();

        foreach ($contents as $file) {

            $page = $this->parseContentFile($file['basename']);

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
        $contents = $contents = $this->filesystem->listContents();

        $articles = array_map(function ($file) {
            return $this->parseContentFile($file['basename']);
        }, $contents);

        if ($tag) {
            $articles = $this->filterByTag($articles, $tag);
        }

        $articles = array_values($articles);

        usort($articles, function($a, $b){
            if ($a['date'] == $b['date']) {
                return 0;
            }
            return ($a['date'] > $b['date']) ? -1 : 1;
        });

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

    private function parseContentFile(string $file) : array
    {
        if (!$this->filesystem->has($file)) {
            throw new \Exception('Page content not found');
        }
        $contentRaw = $this->filesystem->read($file);
        if (empty($contentRaw)) {
            throw new \Exception('Invalid content file.');
        }

        if ($this->isJsonMeta($contentRaw)) {
            list($data, $content) = $this->parseJsonMeta($contentRaw);
        } else if ($this->isJekyllMeta($contentRaw)) {
            list($data, $content) = $this->parseJekyllMeta($contentRaw, $file);
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
    private function parseJekyllMeta(string $contentRaw, string $file): array
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
            $data['date'] = (new Datetime($data['date']))->format('Y-m-d');
        } else {
            $data['date'] = substr($file, 0, 10);
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

        $articlePath = $data['date'].'-'.$data['slug'].'.md';

        $this->filesystem->put($articlePath, $content);
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
