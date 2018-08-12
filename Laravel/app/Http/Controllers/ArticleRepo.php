<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Cocur\Slugify\Slugify;
use Symfony\Component\Yaml\Yaml;

class ArticleRepo
{
    public function find(string $slug): array
    {
        $pagePath = app_path('../../contents/articles/');

        $pageFilenames = array_diff(scandir($pagePath), array('..', '.'));

        foreach ($pageFilenames as $pageFilename) {
            $pathToArticle = $pagePath.$pageFilename;

            $page = $this->parseContentFile($pathToArticle);

            if ($page['slug'] == $slug) {
                return $page;
            }
        }

        throw new \Exception('Page content not found');
    }

    public function list(?string $tag): array
    {
        $pagePath = app_path('../../contents/articles/');

        $pageFilenames = array_reverse(array_diff(scandir($pagePath), array('..', '.')));

        $articles = array_map(function ($pageFilename) use ($pagePath) {
            $pathToArticle = $pagePath.$pageFilename;

            return $this->parseContentFile($pathToArticle);
        }, $pageFilenames);

        if ($tag) {
            $articles = $this->filterByTag($articles, $tag);
        }

        return array_values($articles);
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
        $data['categories'] = $data['tags'] ?? [];
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
}
