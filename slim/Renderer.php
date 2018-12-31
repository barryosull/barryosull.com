<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class Renderer
{
    public function render(array $page)
    {
        $page = (object)$page;
        require __DIR__ . "/templates/default.php";
    }
}
