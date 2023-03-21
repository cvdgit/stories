<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students;

use yii\db\Expression;
use yii\db\Query;

class StudentsWithRepetitionFetcher
{
    /**
     * @return array<int, int>
     */
    public function fetch(): array
    {
        $rows = (new Query())
            ->select(['studentId' => new Expression('DISTINCT student_id')])
            ->from('test_repetition')
            ->all();
        return array_column($rows, 'studentId');
    }
}
