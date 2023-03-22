<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students\CreateRepetition;

use common\models\UserStudent;
use yii\base\Action;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class CreateRepetitionAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $id): string
    {
        $student = UserStudent::findOne($id);
        if ($student === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $noNextQuery = (new Query())
            ->from(['r2' => 'test_repetition'])
            ->where('r2.test_id = r.test_id AND r2.student_id = r.student_id AND r2.done = 0');

        $rows = (new Query())
            ->select([
                'testId' => 't.id',
                'testName' => 't.header',
                'scheduleId' => 's.id',
                'scheduleName' => 's.name',
            ])
            ->from(['r' => 'test_repetition'])
            ->innerJoin(['t' => 'story_test'], 'r.test_id = t.id')
            ->innerJoin(['i' => 'schedule_item', 'r.schedule_item_id = i.id'])
            ->innerJoin(['s' => 'schedule'], 'i.schedule_id = s.id')
            ->where(['r.student_id' => $student->id])
            ->andWhere(['not exists', $noNextQuery])
            ->groupBy(['testId', 'testName', 'scheduleId', 'scheduleName'])
            ->all();

        return $this->controller->renderAjax('_create', [
            'rows' => $rows,
            'studentId' => $student->id,
        ]);
    }
}
