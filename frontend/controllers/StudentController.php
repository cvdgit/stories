<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserStudent;
use frontend\models\CreateStudentForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StudentController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new CreateStudentForm(Yii::$app->user->id);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->createStudent();
                $user = User::findModel(Yii::$app->user->id);
                return Json::encode(['success' => true, 'students' => $user->getStudentsAsArray()]);
            }
            catch (\Exception $ex) {
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = UserStudent::findModel($id);
        if (!$model->userOwned(Yii::$app->user->id)) {
            throw new NotFoundHttpException ('Пользователь не найден');
        }
        $model->delete();
        return ['success' => true];
    }

}