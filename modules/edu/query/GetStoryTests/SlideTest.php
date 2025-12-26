<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

class SlideTest implements SlideContentItemInterface
{
    private $slideId;
    private $slideNumber;
    private $testId;
    /**
     * @var string
     */
    private $content;

    public function __construct(int $slideId, int $slideNumber, int $testId, string $content)
    {
        $this->slideId = $slideId;
        $this->slideNumber = $slideNumber;
        $this->testId = $testId;
        $this->content = $content;
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

    public function getContent(): string
    {
        return $this->content;
    }
}
