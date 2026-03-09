<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\parsers;

use modules\edu\query\GetStoryTests\SlideRetelling;

class SlideRetellingParser implements ContentParseInterface
{
    public function __construct(SlideRetelling $contentItem) {}

    public function parse(): int
    {
        return 1;
    }
}
