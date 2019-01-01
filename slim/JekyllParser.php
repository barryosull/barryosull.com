<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

class JekyllParser
{
    public function parseJekyllMarkdownFile($pathToFile): Article
    {
        $contentRaw = file_get_contents($pathToFile);
        if (empty($contentRaw)) {
            throw new \Exception('Invalid content file.');
        }

        $sections = explode('---', $contentRaw);
        $data = Yaml::parse($sections[1], Yaml::PARSE_DATETIME);

        $article = new Article();

        $article->title = $data['title'];
        $article->published = $data['published'] ?? true;
        $article->slug = $data['slug'] ?? Strings::webalize($data['title']);
        $article->author = $data['author'] ?? 'Barry';
        $article->date = (isset($data['date']))
            ? $data['date']->format('Y-m-d')
            : $this->getDateFromFilename($pathToFile);
        $article->categories = (isset($data['tags']))
            ? $this->getCategoriesFromArticle($data)
            : [];
        $article->url = "/blog/" . $article->slug;
        $article->coverImage = $data['cover_image'] ?? null;
        $article->content = $this->getMarkdownContents($sections);
        $article->excerpt = $this->getExcerpt($article->slug, $article->content);

        return $article;
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

    /**
     * @param $pathToFile
     * @return string
     */
    private function getDateFromFilename($pathToFile): string
    {
        $file_name = last(explode("/", $pathToFile));
        return substr($file_name, 0, 10);
    }

    /**
     * @param $data
     * @return array
     */
    private function getCategoriesFromArticle($data): array
    {
        $categoriesString = $data['tags'] ?? '';
        $categories = explode(",", $categoriesString);

        $categories = array_map(function ($category) {
            return strtolower(trim($category));
        }, $categories);
        return $categories;
    }

    /**
     * @param $sections
     * @return string
     */
    private function getMarkdownContents($sections): string
    {
        $content = trim(
            implode("---",
                array_values(
                    array_slice($sections, 2)
                )
            )
        );
        return $content;
    }
}
