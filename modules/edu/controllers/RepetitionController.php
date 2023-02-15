<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use common\models\StoryTest;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RepetitionController extends Controller
{
    public $layout = '@frontend/views/layouts/edu';

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $testing = StoryTest::findOne($id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $studentId = \Yii::$app->studentContext->getId();
        if ($studentId === null) {
            $studentId = \Yii::$app->user->identity->getStudentID();
        }

        return $this->render('view', [
            'testing' => $testing,
            'studentId' => $studentId,
        ]);
    }
}
