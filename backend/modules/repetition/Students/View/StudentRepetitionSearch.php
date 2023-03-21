<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students\View;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;

class StudentRepetitionSearch extends Model
{
    public function search(int $studentId): DataProviderInterface
    {
        $query = (new Query())
            ->select([
                'testName' => 'st.header',
                'testId' => 'st.id',
                'scheduleName' => 's.name',
                'scheduleId' => 's.id',
                'allDone' => 'GROUP_CONCAT(r.done ORDER BY r.created_at)',
                'lastItem' => new Expression('MAX(r.created_at)'),
                'scheduleItemsCount' => (new Query())
                    ->select(new Expression('count(t.id)'))
                    ->from(['t' => 'schedule_item'])
                    ->where('s.id = t.schedule_id'),
                'repetitionItemsCount' => new Expression('COUNT(IF(r.done = 1, r.id, null))'),
            ])
            ->from(['r' => 'test_repetition'])
            ->innerJoin(['st' => 'story_test'], 'r.test_id = st.id')
            ->innerJoin(['si' => 'schedule_item'], 'r.schedule_item_id = si.id')
            ->innerJoin(['s' => 'schedule'], 'si.schedule_id = s.id')
            ->where([
                'r.student_id' => $studentId,
            ])
            ->groupBy(['testId', 'testName', 'scheduleId', 'scheduleName']);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'pagination' => false,
        ]);
    }
}
