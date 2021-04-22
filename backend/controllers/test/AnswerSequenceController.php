<?php

namespace backend\controllers\test;

use common\models\StoryTestAnswer;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class AnswerSequenceController extends Controller
{

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return ['success' => true];
    }

    /**
     * @param $id
     * @return StoryTestAnswer|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?StoryTestAnswer
    {
        if (($model = StoryTestAnswer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}