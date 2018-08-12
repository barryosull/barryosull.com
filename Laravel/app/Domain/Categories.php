<?php
declare(strict_types=1);

namespace App\Domain;

class Categories
{
    private $data;

    // TODO: Write test for rejection of non strings
    public static function fromArray(array $array): self
    {
        $categories =  new self;

        array_map(function($category){
            if (!is_string($category)) {
                throw new ValueException("Category cannot be a string");
            }
            if ($category == "") {
                throw new ValueException("Category cannot be blank");
            }
        }, $array);

        $categories->data = $array;
        return $categories;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
