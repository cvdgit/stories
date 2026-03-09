<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\parsers;

interface ContentParseInterface
{
    public function parse(): int;
}
