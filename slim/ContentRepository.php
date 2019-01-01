<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

class ContentRepository
{
    const CONTENTS_DIR = __DIR__ . "/../contents";

    const ARTICLES_DIR = self::CONTENTS_DIR . "/articles/";

    public function fetchPage(string $page) : \stdClass
    {
        $pathToFile = self::CONTENTS_DIR . "/pages/" . $page . ".md";

        if (!file_exists($pathToFile)) {
            throw new \Exception('Page content not found');
        }

        $data = $this->parseJekyllMetaData($pathToFile);

        return (object)$data;
    }

    public function fetchArticleBySlug(string $articleSlug) : \stdClass
    {
        $dir = self::ARTICLES_DIR;

        $files = scandir($dir);

        foreach($files as $file) {

            if (strpos($file, ".md") === false) {
                continue;
            }

            $data = $this->parseJekyllMetaData($dir . $file);

            if ($data['slug'] == $articleSlug) {
                return (object)$data;
            }
        }

        throw new \Exception('Article content not found');
    }

    public function fetchCollection(?string $category = null, bool $includeDraft = false): array
    {
        $dir = self::ARTICLES_DIR;

        $files = scandir($dir);

        $articles = [];

        foreach($files as $file) {

            if (strpos($file, ".md") === false) {
                continue;
            }

            $articles[] = (object)$this->parseJekyllMetaData($dir . $file);
        }

        $articles = array_reverse($articles);

        if (!$includeDraft) {
            $articles = array_filter($articles, function($article){
               return $article->published;
            });
        }

        if ($category) {
            $articles = array_filter($articles, function($article) use ($category) {
                return in_array($category, $article->categories);
            });
        }

        return array_values($articles);
    }

    public function fetchAllCategories(): array
    {
        $dir = self::ARTICLES_DIR;

        $files = scandir($dir);

        $categories = [];

        foreach($files as $file) {

            if (strpos($file, ".md") === false) {
                continue;
            }

            $article = $this->parseJekyllMetaData($dir . $file);

            $categories = array_merge($categories, $article['categories'] ?? []);
        }

        sort($categories);
        $categories = array_unique($categories);

        return $categories;
    }

    private function parseJekyllMetaData($pathToFile): array
    {
        $contentRaw = file_get_contents($pathToFile);
        if (empty($contentRaw)) {
            throw new \Exception('Invalid content file.');
        }

        $sections = explode('---', $contentRaw);
        $data = Yaml::parse($sections[1], Yaml::PARSE_DATETIME);

        if (!isset($data['slug'])) {
            $data['slug'] = Strings::webalize($data['title']);
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
        if (isset($data['tags'])) {
            $categoriesString = $data['tags'] ?? '';
            $categories = explode(",", $categoriesString);

            $data['categories'] = array_map(function($category){
                return strtolower(trim($category));
            }, $categories);
        }

        $data['url'] = "/blog/" . $data['slug'];

        $data['coverImage'] = $data['cover_image'] ?? null;

        $content = trim(
            implode("---",
                array_values(
                    array_slice($sections, 2)
                )
            )
        );

        $data['content'] = $content;

        $data['excerpt'] = $this->getExcerpt($data['slug'], $content);

        return $data;
    }

    const EXERT_LENGTH = 640;

    private function getExcerpt(string $slug, string $content) : string
    {
        if (empty($content)) {
            return '';
        }
        $moreMarkerPosition = strpos($content, '<!--more-->');
        if (empty($moreMarkerPosition)) {
            $moreMarkerPosition = self::EXERT_LENGTH;
        }
        $excerpt = substr($content, 0, $moreMarkerPosition). "... <br>";

        $readMoreLink = $this->readMoreLink("/blog/" . $slug);
        $excerpt .= $readMoreLink;

        return $excerpt;
    }

    private function readMoreLink(string $url): string
    {
        return '<a href="' . $url . '" style="float:right" class="btn">Read on &raquo;</a>';
    }
}
