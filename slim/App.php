<?php
declare(strict_types=1);

namespace Barryosull\Slim;

use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

class App
{
    public function run()
    {
        $configuration = [
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ];
        $c = new \Slim\Container($configuration);

        $slimApp = new \Slim\App($c);

        $app = $this;

        $slimApp->get('/', function ($request, $response, $args) use ($app) {

            $pathToContentFile = __DIR__ . "/../contents/pages/home.md";

            $page = $app->parseContentFile($pathToContentFile);

            $app->render($page);
        });

        $slimApp->run();
        return;
    }

    private function render(array $page)
    {
        $page = (object)$page;
        require __DIR__ . "/templates/default.php";
    }

    private function parseContentFile(string $pathToFile, bool $includeContent = true) : array
    {
        if (!file_exists($pathToFile)) {
            throw new \Exception('Page content not found');
        }

        $data = $this->parseJekyllMeta($pathToFile);

        //echo "<pre>"; var_dump($data); exit;

        return $data;
    }

    private function parseJekyllMeta($pathToFile): array
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
