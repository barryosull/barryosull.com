<?php

namespace Tests\Acceptance\Support;

use Psr\Http\Message\ResponseInterface;

interface App
{
    public function visitUrl(string $url): ResponseInterface;
}