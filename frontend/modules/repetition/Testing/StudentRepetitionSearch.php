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
    private function createMentalMapQuery(int $studentId): Query
    {
        $repetitionQuery = (new Query())
            ->select(new Expression('r.created_at + (i.hours * 60 * 60)'))
            ->from(['i' => 'schedule_item'])
            ->leftJoin(['r' => 'mental_map_repetition'], 'i.id = r.schedule_item_id')
            ->where('t.schedule_id = i.schedule_id')
            ->andWhere('r.mental_map_id = t.uuid')
            ->andWhere(['r.student_id' => $studentId])
            ->andWhere('r.done = 0');

        $scheduleTotalQuery = (new Query())
            ->select('count(i.id)')
            ->from(['i' => 'schedule_item'])
            ->where('t.schedule_id = i.schedule_id');

        $repetitionDoneQuery = (new Query())
            ->select('count(r.id)')
            ->from(['i' => 'schedule_item'])
            ->leftJoin(['r' => 'mental_map_repetition'], 'i.id = r.schedule_item_id')
            ->where('t.schedule_id = i.schedule_id')
            ->andWhere('r.mental_map_id = t.uuid')
            ->andWhere(['r.student_id' => $studentId]);

        $studentMentalMapIds = (new Query())
            ->select(['mentalMapId' => new Expression('DISTINCT mental_map_id')])
            ->from('mental_map_repetition')
            ->where(['student_id' => $studentId])
            ->andWhere('done = 0')
            ->all();
        $studentMentalMapIds = array_column($studentMentalMapIds, 'mentalMapId');

        return (new Query())
            ->select([
                'id' => 't.uuid',
                new Expression('t.name AS header'),
                'date' => $repetitionQuery,
                'totalItems' => $scheduleTotalQuery,
                'doneItems' => $repetitionDoneQuery,
                "'mental_map' AS `obj`",
            ])
            ->from(['t' => 'mental_map'])
            ->where(['in', 't.uuid', $studentMentalMapIds])
            ->andWhere(['>', new Expression('UNIX_TIMESTAMP()'), $repetitionQuery]);
    }

    private function createTestingQuery(int $studentId): Query
    {
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

        return (new Query())
            ->select([
                'id' => 't.id',
                new Expression('t.header AS header'),
                'date' => $repetitionQuery,
                'totalItems' => $scheduleTotalQuery,
                'doneItems' => $repetitionDoneQuery,
                "'test' AS `obj`",
            ])
            ->from(['t' => 'story_test'])
            ->where(['in', 't.id', $studentTestIds])
            ->andWhere(['>', new Expression('UNIX_TIMESTAMP()'), $repetitionQuery]);
    }

    public function search(int $studentId): DataProviderInterface
    {
        $testingQuery = $this->createTestingQuery($studentId);
        $mentalMapQuery = $this->createMentalMapQuery($studentId);

        $query = (new Query())
            ->select('t.*')
            ->from(['t' => $testingQuery->union($mentalMapQuery)])
            ->orderBy(['t.header' => SORT_ASC]);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_ASC,
                    'header' => SORT_ASC,
                ],
                'attributes' => [
                    'date',
                    'header',
                ],
            ],
        ]);
    }
}
