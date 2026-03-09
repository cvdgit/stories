<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\fetchers;

use DateTimeInterface;
use yii\db\Expression;

abstract class AbstractFetcher
{
    protected function getBetweenDates(DateTimeInterface $date): array
    {
        $targetDate = $date->format('Y-m-d');
        return [
            new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')"),
            new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')"),
        ];
    }
}
