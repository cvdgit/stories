<?php

declare(strict_types=1);

namespace frontend\modules\repetition\Testing;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;

class StudentRepetitionSearch extends Model
{
    public function search(int $studentId): DataProviderInterface
    {
        $testingCompletedQuery = (new Query())
            ->from(['p' => 'student_question_progress'])
            ->where('p.test_id = t.id')
            ->andWhere(['p.student_id' => $studentId])
            ->andWhere('p.progress = 100');

        $repetitionQuery = (new Query())
            ->select(new Expression('r.created_at + (i.hours * 60 * 60)'))
            ->from(['i' => 'schedule_item'])
            ->leftJoin(['r' => 'test_repetition'], 'i.id = r.schedule_item_id')
            ->where('t.schedule_id = i.schedule_id')
            ->andWhere('r.test_id = t.id')
            ->andWhere(['r.student_id' => $studentId])
            ->andWhere('r.done = 0');

        $scheduleTotalQuery = (new Query())
            ->select('count(i.id)')
            ->from(['i' => 'schedule_item'])
            ->where('t.schedule_id = i.schedule_id');

        $repetitionDoneQuery = (new Query())
            ->select('count(r.id)')
            ->from(['i' => 'schedule_item'])
            ->leftJoin(['r' => 'test_repetition'], 'i.id = r.schedule_item_id')
            ->where('t.schedule_id = i.schedule_id')
            ->andWhere('r.test_id = t.id')
            ->andWhere(['r.student_id' => $studentId]);

        $studentTestIds = (new Query())
            ->select(['testId' => new Expression('DISTINCT test_id')])
            ->from('test_repetition')
            ->where(['student_id' => $studentId])
            ->andWhere('done = 0')
            ->all();
        $studentTestIds = array_column($studentTestIds, 'testId');

        $query = (new Query())
            ->select([
                't.id',
                't.header',
                'date' => $repetitionQuery,
                'totalItems' => $scheduleTotalQuery,
                'doneItems' => $repetitionDoneQuery,
            ])
            ->from(['t' => 'story_test'])
            ->where(['in', 't.id', $studentTestIds])
            //->where('t.schedule_id IS NOT NULL')
            //->andWhere(['exists', $testingCompletedQuery])
            ->andWhere(['>', new Expression('UNIX_TIMESTAMP()'), $repetitionQuery]);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'pagination' => [
                'pageSize' => 4,
            ],
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_ASC
                ],
                'attributes' => [
                    'date',
                ],
            ],
        ]);
    }
}
