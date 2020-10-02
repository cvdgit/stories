<?php

namespace backend\controllers;

use backend\models\StudentTestHistory;
use common\models\StoryTest;
use common\models\UserStudent;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HistoryController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_USERS],
                    ],
                ],
            ],
        ];
    }

    public function actionView(int $id)
    {
        $studentModel = $this->findStudentModel($id);
        $history = new StudentTestHistory($studentModel->id);
        return $this->render('view', [
            'student' => $studentModel,
            'history' => $history,
        ]);
    }

    protected function findStudentModel($id)
    {
        if (($model = UserStudent::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findTestModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionClear(int $student_id, int $test_id)
    {
        $studentModel = $this->findStudentModel($student_id);
        $testModel = $this->findTestModel($test_id);
        (new StudentTestHistory($studentModel->id))->clearTestHistory($testModel->id);
        Yii::$app->session->setFlash('success', 'История прохождения теста удалена');
        return $this->redirect(['view', 'id' => $student_id]);
    }

}