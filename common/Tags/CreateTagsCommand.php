<?php

declare(strict_types=1);

namespace common\Tags;

class CreateTagsCommand
{
    private $tag;

    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
