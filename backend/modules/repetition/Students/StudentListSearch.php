<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;

class StudentListSearch extends Model
{
    public function search(array $params = []): DataProviderInterface
    {
        $studentIds = (new StudentsWithRepetitionFetcher())->fetch();

        $query = (new Query())
            ->select([
                'studentName' => 'us.name',
                'studentId' => 'us.id',
                'scheduleName' => 's.name',
                'scheduleId' => 's.id',
                'testsCount' => new Expression('COUNT(DISTINCT r.test_id)'),
            ])
            ->from(['r' => 'test_repetition'])
            ->innerJoin(['us' => 'user_student'], 'r.student_id = us.id')
            ->innerJoin(['si' => 'schedule_item'], 'r.schedule_item_id = si.id')
            ->innerJoin(['s' => 'schedule'], 'si.schedule_id = s.id')
            ->where(['in', 'r.student_id', $studentIds])
            ->groupBy(['studentId', 'studentName', 'scheduleId', 'scheduleName']);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'pagination' => false,
        ]);
    }
}
