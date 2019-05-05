<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class UrlService
{
    private $contentRepository;

    public function __construct()
    {
        $this->contentRepository = new ContentRepository();
    }

    /**
     * @return string[]
     */
    public function getAvailableUrls(): array
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
        $articles = $this->contentRepository->fetchCollection();

        return array_map(function(Article $article){
            return $article->url;
        }, $articles);
    }

    /**
     * @return string[]
     */
    private function getBlogPageUrls(): array
    {
        $articles = $this->contentRepository->fetchCollection();

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
        $categories = $this->contentRepository->fetchAllCategories();

        $urls = [];

        foreach ($categories as $category) {
            $articles = $this->contentRepository->fetchCollection($category);
            $pageCount = BlogController::getPageCount($articles);

            for ($i = 0; $i < $pageCount; $i++) {
                $page = ($i == 0) ? '' : '/page-' . $i;
                $urls[] = "/blog/category/$category" . $page;
            }
        }

        return $urls;
    }
}
