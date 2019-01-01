<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class ContentRepository
{
    const CONTENTS_DIR = __DIR__ . "/../contents";

    const ARTICLES_DIR = self::CONTENTS_DIR . "/articles/";

    private $fileParser;

    public function __construct()
    {
        $this->fileParser = new MarkdownParser();
    }

    public function fetchPage(string $page) : \stdClass
    {
        $pathToFile = self::CONTENTS_DIR . "/pages/" . $page . ".md";

        if (!file_exists($pathToFile)) {
            throw new \Exception('Page content not found');
        }

        $data = $this->fileParser->parseJekyllMarkdownFile($pathToFile);

        return $data;
    }

    public function fetchArticleBySlug(string $articleSlug) : \stdClass
    {
        $dir = self::ARTICLES_DIR;

        $files = scandir($dir);

        foreach($files as $file) {

            if (strpos($file, ".md") === false) {
                continue;
            }

            $data = $this->fileParser->parseJekyllMarkdownFile($dir . $file);

            if ($data->slug == $articleSlug) {
                return $data;
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

            $articles[] = $this->fileParser->parseJekyllMarkdownFile($dir . $file);
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

            $article = $this->fileParser->parseJekyllMarkdownFile($dir . $file);

            $categories = array_merge($categories, $article->categories ?? []);
        }

        sort($categories);
        $categories = array_unique($categories);

        return $categories;
    }
}
