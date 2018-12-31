<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

class PageRepository
{
    public function fetchPage(string $page) : array
    {
        $pathToFile = __DIR__ . "/../contents/pages/" . $page . ".md";

        if (!file_exists($pathToFile)) {
            throw new \Exception('Page content not found');
        }

        $data = $this->parseJekyllMetaData($pathToFile);

        return $data;
    }

    public function fetchArticleBySlug(string $articleSlug) : array
    {
        $dir = __DIR__ . "/../contents/articles/";

        $files = scandir($dir);

        foreach($files as $file) {

            if (strpos($file, ".md") === false) {
                continue;
            }

            $data = $this->parseJekyllMetaData($dir . $file);

            if ($data['slug'] == $articleSlug) {
                return $data;
            }
        }

        throw new \Exception('Article content not found');
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
            $data['categories'] = $data['tags'] ?? [];
        }

        $data['coverImage'] = $data['cover_image'] ?? null;

        $content = trim(
            implode("---",
                array_values(
                    array_slice($sections, 2)
                )
            )
        );

        $data['content'] = $content;

        return $data;
    }
}
