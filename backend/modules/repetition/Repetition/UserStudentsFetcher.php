<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition;

use yii\db\Query;

class UserStudentsFetcher
{
    /**
     * @return array<int, int>
     */
    public function fetch(): array
    {
        $rows = (new Query())
            ->select(['studentId' => 's.id'])
            ->from(['s' => 'user_student'])
            ->innerJoin(['u' => 'user'], 's.user_id = u.id')
            ->where('u.status = 10')
            ->all();
        return array_column($rows, 'studentId');
    }
}
