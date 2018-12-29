<?php
declare(strict_types=1);
namespace Nekudo\ShinyBlog\Domain;

use DirectoryIterator;
use Nekudo\ShinyBlog\Domain\Entity\ArticleEntity;
use Nette\Utils\Strings;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class ContentDomain extends BaseDomain
{
    /** @var string $contentRaw */
    protected $contentRaw = '';

    /** @var array $articleMeta */
    protected $articleMeta = [];

    /** @var array $pageMeta */
    protected $pageMeta = [];

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Loads metadata of articles.
     *
     * @param string $keyName Name of value to use as array key.
     * @param bool $forceReload Reload metadata even if already loaded
     * @return ContentDomain
     */
    protected function loadArticlesMeta(string $keyName, bool $forceReload = false) : ContentDomain
    {
        if (!empty($this->articleMeta) && $forceReload === false) {
            return $this;
        }
        $pathToArticleContents = $this->config['contentsFolder'] . 'articles/';
        if (!is_dir($pathToArticleContents)) {
            throw new RuntimeException('Articles folder not found.');
        }

        $this->articleMeta = $this->getContentMeta($pathToArticleContents, $keyName);

        return $this;
    }

    public function filterOutUnpublished()
    {
        $this->articleMeta = array_filter($this->articleMeta, function($article){
            return isset($article['published']) && $article['published'] == true;
        });
    }

    public function filterOutPublished()
    {
        $this->articleMeta = array_filter($this->articleMeta, function($article){
            return isset($article['published']) && $article['published'] == false;
        });
    }

    /**
     * Loads metadata of pages.
     *
     * @param string $keyName Name of value to use as array key.
     * @param bool $forceReload Reload metadata even if already loaded
     * @return bool
     */
    protected function loadPageMeta(string $keyName, bool $forceReload = false) : bool
    {
        if (!empty($this->pageMeta) && $forceReload === false) {
            return true;
        }
        $pathToPageContents = $this->config['contentsFolder'] . 'pages/';
        if (!is_dir($pathToPageContents)) {
            throw new RuntimeException('Pages folder not found.');
        }
        $this->pageMeta = $this->getContentMeta($pathToPageContents, $keyName);
        return true;
    }

    /**
     * Fetches content metadata from given folder.
     *
     * @param string $contentFolder
     * @param string $keyName Name of value to use as array key. (e.g. "slug")
     * @return array
     */
    protected function getContentMeta(string $contentFolder, string $keyName) : array
    {
        $metadata = [];
        $iterator = new DirectoryIterator($contentFolder);
        foreach ($iterator as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($file->getExtension() !== 'md') {
                continue;
            }
            $itemMeta = $this->parseContentFile($file->getPathname(), false);
            if (empty($itemMeta[$keyName])) {
                throw new RuntimeException("Key '$keyName' not found in items metadata.");
            }
            $key = $itemMeta[$keyName];
            $metadata[$key] = $itemMeta;
            $metadata[$key]['file'] = $file->getPathname();
            $metadata[$key]['lastmod'] = $file->getMTime();
        }
        return $metadata;
    }

    /**
     * Loads articles from set of metadata.
     *
     * @param int $start
     * @param int $end
     * @return array
     */
    protected function getArticlesFromMeta(int $start = 0, int $end = 0) : array
    {
        $articles = [];
        if (empty($this->articleMeta)) {
            return $articles;
        }
        $metaCount = count($this->articleMeta);
        if ($start >= $metaCount) {
            throw new RuntimeException('Start value can not be greater than total items.');
        }
        if ($end >= $metaCount) {
            throw new RuntimeException('End value can not be greater than total items.');
        }
        if ($end === 0) {
            $end = $metaCount - 1;
        }
        $keys = array_keys($this->articleMeta);
        for ($i = $start; $i <= $end; $i++) {
            $key = $keys[$i];
            $articleData = $this->parseContentFile($this->articleMeta[$key]['file']);
            $article = new ArticleEntity($this->config, $articleData);
            array_push($articles, $article);
        }
        return $articles;
    }

    /**
     * Parses a content-file and splits data into a meta and content section.
     *
     * @param string $pathToFile
     * @param bool $includeContent
     * @return array
     */
    public function parseContentFile(string $pathToFile, bool $includeContent = true) : array
    {
        if (!file_exists($pathToFile)) {
            throw new RuntimeException('Page content not found');
        }
        $this->contentRaw = file_get_contents($pathToFile);
        if (empty($this->contentRaw)) {
            throw new RuntimeException('Invalid content file.');
        }

        if ($this->isJsonMeta()) {
            list($data, $content) = $this->parseJsonMeta();
        } else if ($this->isJekyllMeta()) {
            list($data, $content) = $this->parseJekyllMeta($pathToFile);
        } else {
            throw new RuntimeException('Invalid content file, missing meta information');
        }

        if ($includeContent === false) {
            return $data;
        }
        $data['content'] = $content;
        return $data;
    }

    // TODO: extract into its own class
    private function parseJsonMeta(): array
    {
        $sections = explode('::METAEND::', $this->contentRaw);
        $data = json_decode($sections[0], true);
        $content = trim($sections[1]);

        return [$data, $content];
    }

    // TODO: extract into its own class
    private function parseJekyllMeta($pathToFile): array
    {
        $sections = explode('---', $this->contentRaw);
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

        return [$data, $content];
    }

    private function isJsonMeta()
    {
        return strpos($this->contentRaw, '::METAEND::') !== false;
    }

    private function isJekyllMeta()
    {
        $sections = explode('---', $this->contentRaw);
        return count($sections) > 1;
    }
}
