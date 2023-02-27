<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingList;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;

class RepetitionListSearch extends Model
{
    public function search(int $testId): DataProviderInterface
    {
        $query = (new Query())
            ->select([
                'studentName' => 'us.name',
                'studentId' => 'us.id',
                'scheduleName' => 's.name',
                'scheduleId' => 's.id',
                'lastItem' => new Expression('MAX(r.created_at)'),
                'scheduleItemsCount' => (new Query())
                    ->select(new Expression('count(t.id)'))
                    ->from(['t' => 'schedule_item'])
                    ->where('s.id = t.schedule_id'),
                'repetitionItemsCount' => new Expression('COUNT(IF(r.done = 1, r.id, null))'),
            ])
            ->from(['r' => 'test_repetition'])
            ->innerJoin(['us' => 'user_student'], 'r.student_id = us.id')
            ->innerJoin(['si' => 'schedule_item'], 'r.schedule_item_id = si.id')
            ->innerJoin(['s' => 'schedule'], 'si.schedule_id = s.id')
            ->where([
                'r.test_id' => $testId,
            ])
            ->groupBy(['studentId', 'studentName', 'scheduleId', 'scheduleName']);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'pagination' => false,
        ]);
    }
}
