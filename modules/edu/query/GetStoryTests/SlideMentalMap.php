<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

class SlideMentalMap implements SlideContentItemInterface
{
    /**
     * @var int
     */
    private $slideId;
    /**
     * @var int
     */
    private $slideNumber;
    /**
     * @var string
     */
    private $mentalMapId;

    public function __construct(int $slideId, int $slideNumber, string $mentalMapId)
    {
        $this->slideId = $slideId;
        $this->slideNumber = $slideNumber;
        $this->mentalMapId = $mentalMapId;
    }

    public function getSlideId(): int
    {
        return $this->slideId;
    }

    public function getSlideNumber(): int
    {
        return $this->slideNumber;
    }

    public function getMentalMapId(): string
    {
        return $this->mentalMapId;
    }
}
