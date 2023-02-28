<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition;

use yii\db\Expression;
use yii\db\Query;

class UserStudentItemsFetcher
{
    /**
     * @return array<int, array{studentId: int, studentName: string}>
     */
    public function fetch(): array
    {
        $studentIds = (new UserStudentsFetcher())->fetch();
        if ($studentIds === []) {
            return [];
        }
        return (new Query())
            ->select([
                'studentId' => 's.id',
                'studentName' => new Expression("CONCAT(s.name, ' (', IF(p.id IS NULL, u.email, CONCAT(p.last_name, ' ', p.first_name)), ')')"),
            ])
            ->from(['s' => 'user_student'])
            ->innerJoin(['u' => 'user'], 's.user_id = u.id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id')
            ->where(['in', 's.id', $studentIds])
            ->orderBy(['s.name' => SORT_ASC])
            ->all();
    }
}
