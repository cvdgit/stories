<?php

declare(strict_types=1);

namespace backend\modules\repetition\query;

use backend\modules\repetition\ScheduleFetcherInterface;
use yii\db\Query;

class ScheduleFetcher implements ScheduleFetcherInterface
{
    /**
     * @return array{int,array{id: int, name: string}}
     */
    public function getSchedules(): array
    {
        return (new Query())
            ->select(['id', 'name'])
            ->from('schedule')
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }
}
