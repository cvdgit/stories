<?php

declare(strict_types=1);

namespace backend\modules\changelog\query;

use yii\db\Query;

class LastChangelogListFetcher
{
    public function fetch(): array
    {
        return (new Query())
            ->select(['title', 'text', 'created_at'])
            ->from('changelog')
            ->where('status = 1')
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }
}
