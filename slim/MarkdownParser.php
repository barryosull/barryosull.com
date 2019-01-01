<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

class MarkdownParser
{
    public function parseJekyllMarkdownFile($pathToFile): \stdClass
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

        return (object)$data;
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
