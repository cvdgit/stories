<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

class SlideRetelling implements SlideContentItemInterface
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
    private $retellingId;

    public function __construct(int $slideId, int $slideNumber, string $retellingId)
    {
        $this->slideId = $slideId;
        $this->slideNumber = $slideNumber;
        $this->retellingId = $retellingId;
    }

    public function getSlideId(): int
    {
        return $this->slideId;
    }

    public function getSlideNumber(): int
    {
        return $this->slideNumber;
    }

    public function getRetellingId(): string
    {
        return $this->retellingId;
    }
}
