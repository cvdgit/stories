<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

class Text implements GuestBlockInterface
{
    /** @var string */
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function isEmpty(): bool
    {
        return empty($this->text);
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
