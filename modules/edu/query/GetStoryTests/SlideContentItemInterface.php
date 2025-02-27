<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

interface SlideContentItemInterface
{
    public function getSlideId(): int;

    public function getSlideNumber(): int;
}
