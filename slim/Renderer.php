<?php
declare(strict_types=1);

namespace Barryosull\Slim;

class Renderer
{
    public function render(string $view, array $data)
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        require __DIR__ . "/views/" . $view . ".php";
    }
}
