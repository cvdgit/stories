<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\parsers;

use modules\edu\query\GetStoryTests\Slide;

class SlideParser implements ContentParseInterface
{
    public function __construct(Slide $contentItem) {}

    public function parse(): int
    {
        return 1;
    }
}
