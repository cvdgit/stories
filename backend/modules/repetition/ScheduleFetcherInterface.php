<?php

declare(strict_types=1);

namespace backend\modules\repetition;

interface ScheduleFetcherInterface
{
    public function getSchedules(): array;
}
