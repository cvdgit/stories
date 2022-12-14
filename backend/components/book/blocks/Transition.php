<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

class Transition implements GuestBlockInterface
{
    private $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function isEmpty(): bool
    {
        return empty($this->title);
    }
}
