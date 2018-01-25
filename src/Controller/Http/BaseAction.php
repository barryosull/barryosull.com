<?php
declare(strict_types=1);
namespace Nekudo\ShinyBlog\Controller\Http;

abstract class BaseAction
{
    protected $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }
}
