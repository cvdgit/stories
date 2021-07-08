<?php

namespace backend\controllers;

use backend\components\BaseController;
use common\models\StoryTestAnswer;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class AnswerController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionDeleteImage(int $id)
    {
        /** @var StoryTestAnswer $model */
        $model = $this->findModel(StoryTestAnswer::class, $id);
        $fileDeleted = false;
        try {
            $model->deleteImage();
            $fileDeleted = true;
        }
        catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        $model->image = null;
        $model->save();
        Yii::$app->session->setFlash('success', 'Изображение успешно удалено');
        return $this->redirect(['test/update-answer', 'answer_id' => $model->id]);
    }

}