<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use ParsedownExtra;

class PageController
{
    private $pageRepo;
    private $parsedown;

    public function __construct(PageRepo $pageRepo, ParsedownExtra $parsedown)
    {
        $this->pageRepo = $pageRepo;
        $this->parsedown = $parsedown;
    }

    public function get($pageSlug)
    {
        $page = $this->pageRepo->find($pageSlug);

        $page = $this->formatResponse($page);

        return response()->json($page);
    }

    private function formatResponse($page): array
    {
        $page['content'] = $this->parsedown->parse($page['content']);
        return $page;
    }
}
