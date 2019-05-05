<?php
declare(strict_types=1);

namespace Tests\Acceptance\Support;

use Barryosull\Slim\BlogController;
use Barryosull\Slim\ContentRepository;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class AppHttp
{
    public function visitUrl(string $url): ResponseInterface
    {
        $app = new HttpServer;
        $app->boot();

        $client = HttpServer::makeClient();

        $response = $client->get($url);

        return $response;
    }

    /**
     * @return string[]
     */
    public function getUrls(): array
    {
        $staticUrls = [
            "/",
            "/talks",
            "/blog/feed",
        ];

        $articleUrls = $this->getArticleUrls();
        $blogPageUrls = $this->getBlogPageUrls();
        $blogCategoryPageUrls = $this->getBlogCategoryPageUrls();

        return array_merge(
            $staticUrls,
            $articleUrls,
            $blogPageUrls,
            $blogCategoryPageUrls
        );
    }

    /**
     * @return string[]
     */
    private function getArticleUrls(): array
    {
        $url = '/blog/feed';

        $response = $this->visitUrl($url);

        $xmlString = strval($response->getBody());

        $xml = new SimpleXMLElement($xmlString);

        $domain = getenv('DOMAIN');

        $urls = [];
        foreach ($xml->channel->item as $item) {
            $urls[] = str_replace($domain, "", strval($item->link));
        }

        return $urls;
    }

    /**
     * @return string[]
     */
    private function getBlogPageUrls(): array
    {
        $contentRepository = new ContentRepository();
        $articles = $contentRepository->fetchCollection();

        $pageCount = BlogController::getPageCount($articles);

        $urls = [];
        for ($i = 0; $i < $pageCount; $i++) {
            $page = ($i == 0) ? '' : '/page-' . $i;
            $urls[] = "/blog" . $page;
        }
        return $urls;
    }

    /**
     * @return string[]
     */
    private function getBlogCategoryPageUrls(): array
    {
        $contentRepository = new ContentRepository();
        $categories = $contentRepository->fetchAllCategories();

        $urls = [];

        foreach ($categories as $category) {
            $articles = $contentRepository->fetchCollection($category);
            $pageCount = BlogController::getPageCount($articles);

            for ($i = 0; $i < $pageCount; $i++) {
                $page = ($i == 0) ? '' : '/page-' . $i;
                $urls[] = "/blog/category/$category" . $page;
            }
        }

        return $urls;
    }
}
