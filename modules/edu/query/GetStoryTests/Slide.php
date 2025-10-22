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

    public function __construct(int $id, int $number)
    {
        $this->id = $id;
        $this->number = $number;
    }

    public function getSlideId(): int
    {
        return $this->id;
    }

    public function getSlideNumber(): int
    {
        return $this->number;
    }
}
