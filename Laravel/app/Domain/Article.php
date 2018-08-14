<?php
declare(strict_types=1);

namespace App\Domain;

use DateTime;

class Article
{
    private $title;
    private $description;
    private $slug;
    private $date;
    private $author;
    private $categories;
    private $published;
    private $content;
    private $coverImage;

    public static function fromArray(array $array): self
    {
        return new Article(
            $array['title'],
            $array['description'],
            $array['slug'],
            $array['date'],
            $array['author'],
            Categories::fromArray($array['categories']),
            $array['published'],
            $array['content'],
            $array['coverImage']
        );
    }

    public function __construct(
        ?string $title,
        ?string $description,
        ?string $slug,
        string $date,
        ?string $author,
        Categories $categories,
        bool $published,
        ?string $content,
        ?string $coverImage
    )
    {
        if ($coverImage && !filter_var($coverImage, FILTER_VALIDATE_URL)) {
            throw new ValueException("Invalid coverimage URL '$coverImage'");
        }
        if (!$this->isValidDate($date)) {
            throw new ValueException("Invalid date '$date'");
        }

        $this->title = $title;
        $this->description = $description;
        $this->slug = $slug;
        $this->date = $date;
        $this->author = $author;
        $this->categories = $categories;
        $this->published = $published;
        $this->content = $content;
        $this->coverImage = $coverImage;
    }

    private function isValidDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'date' => strval($this->date),
            'author' => strval($this->author),
            'categories' => $this->categories->toArray(),
            'published' => $this->published,
            'content' => strval($this->content),
            'coverImage' => $this->coverImage ? strval($this->coverImage) : null
        ];
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}
