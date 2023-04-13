<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

class SlideTest
{
    private $slideId;
    private $slideNumber;
    private $testId;

    public function __construct(int $slideId, int $slideNumber, int $testId)
    {
        $this->slideId = $slideId;
        $this->slideNumber = $slideNumber;
        $this->testId = $testId;
    }

    public function getSlideId(): int
    {
        return $this->slideId;
    }

    public function getSlideNumber(): int
    {
        return $this->slideNumber;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }
}
