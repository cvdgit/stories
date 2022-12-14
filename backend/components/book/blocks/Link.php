<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

class Link implements GuestBlockInterface
{
    private $title;
    private $href;

    public function __construct(string $title, string $href)
    {
        $this->title = $title;
        $this->href = $href;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function isEmpty(): bool
    {
        return empty($this->title);
    }
}
