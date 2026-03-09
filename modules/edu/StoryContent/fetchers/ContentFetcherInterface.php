<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\fetchers;

use DateTimeInterface;

interface ContentFetcherInterface
{
    public function fetch(int $studentId, DateTimeInterface $date = null): int;

    public function fetchRows(int $studentId, DateTimeInterface $date = null): array;
}
