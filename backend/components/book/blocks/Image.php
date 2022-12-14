<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

class Image implements GuestBlockInterface
{
    /** @var string */
    private $image;

    public function __construct(string $image)
    {
        $this->image = $image;
    }

    public function isEmpty(): bool
    {
        return empty($this->image);
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }
}
