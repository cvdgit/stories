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

    public function __construct(int $id, int $number, string $content)
    {
        $this->id = $id;
        $this->number = $number;
        $this->content = $content;
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
}
