<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

class Slide implements SlideContentItemInterface
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $number;
    /**
     * @var string
     */
    private $content;
    /**
     * @var bool
     */
    private $isFinal;

    public function __construct(int $id, int $number, string $content, bool $isFinal = false)
    {
        $this->id = $id;
        $this->number = $number;
        $this->content = $content;
        $this->isFinal = $isFinal;
    }

    public function getSlideId(): int
    {
        return $this->id;
    }

    public function getSlideNumber(): int
    {
        return $this->number;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }
}
