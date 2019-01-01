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

    public function fetchPage(string $page) : Article
    {
        $pathToFile = self::CONTENTS_DIR . "/pages/" . $page . ".md";

        if (!file_exists($pathToFile)) {
            throw new \Exception('Page content not found');
        }

        $data = $this->fileParser->parseJekyllMarkdownFile($pathToFile);

        return $data;
    }

    public function fetchArticleBySlug(string $articleSlug) : Article
    {
        $articles = $this->fetchAllArticles();

        foreach ($articles as $article) {

            if ($article->slug == $articleSlug) {
                return $article;
            }
        }

        throw new \Exception('Article content not found');
    }

    public function fetchCollection(?string $category = null, bool $includeDraft = false): array
    {
        $articles = $this->fetchAllArticles();

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
        $articles = $this->fetchAllArticles();

        $categories = [];

        foreach ($articles as $article) {
            $categories = array_merge($categories, $article->categories ?? []);
        }

        sort($categories);
        $categories = array_unique($categories);

        return $categories;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function fetchAllArticles(): array
    {
        $dir = self::ARTICLES_DIR;

        $files = scandir($dir);

        $articles = [];

        foreach ($files as $file) {

            if (strpos($file, ".md") === false) {
                continue;
            }

            $articles[] = $this->fileParser->parseJekyllMarkdownFile($dir . $file);
        }

        return array_reverse($articles);
    }
}
