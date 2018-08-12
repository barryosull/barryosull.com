<?php
declare(strict_types=1);

namespace Tests\Unit\App\Domain;

use App\Domain\ValueException;
use App\Domain\Categories;
use PHPUnit\Framework\TestCase;

class CategoriesTest extends TestCase
{
    /**
     * @test
     */
    public function it_accepts_an_array_of_strings()
    {
        $array = ['string1', 'string2'];
        $categories = Categories::fromArray($array);

        $this->assertEquals($array, $categories->toArray());
    }

    /**
     * @test
     */
    public function it_rejects_non_strings()
    {
        $this->expectException(ValueException::class);

        $array = ['dffdf', 1245];
        Categories::fromArray($array);
    }

    /**
     * @test
     */
    public function it_rejects_empty_string_values()
    {
        $this->expectException(ValueException::class);

        $array = ['', ''];
        Categories::fromArray($array);
    }

    /**
     * @test
     */
    public function it_accepts_an_empty_array()
    {
        $array = [];
        $categories = Categories::fromArray($array);

        $this->assertEquals($array, $categories->toArray());
    }
}
