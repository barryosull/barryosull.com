<?php
declare(strict_types=1);

namespace Tests\Acceptance;

use Nekudo\ShinyBlog\ShinyBlog;
use PHPUnit\Framework\TestCase;

class UnkownContentTest extends TestCase
{
    public function test_fetching_an_unknown_url()
    {
        $response = AppFactory::make()->visitUrl("/unknown-url");

        $this->assertEquals(404, $response->getStatusCode());
    }
}
