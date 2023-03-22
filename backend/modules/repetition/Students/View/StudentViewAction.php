<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students\View;

use common\models\UserStudent;
use yii\base\Action;
use yii\web\NotFoundHttpException;

class StudentViewAction extends Action
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

        $searchModel = new StudentRepetitionSearch();
        $dataProvider = $searchModel->search($student->id);

        return $this->controller->render('view', [
            'dataProvider' => $dataProvider,
            'studentId' => $student->id,
        ]);
    }
}
