<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

class SlideTableOfContents implements SlideContentItemInterface
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
    private $content;

    public function __construct(int $slideId, int $slideNumber, string $content)
    {
        $this->slideId = $slideId;
        $this->slideNumber = $slideNumber;
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

    public function getContent(): string
    {
        return $this->content;
    }
}
