<?php

namespace frontend\controllers;

use backend\components\BaseController;
use backend\components\training\base\Serializer;
use backend\components\training\collection\TestBuilder;
use common\helpers\UserHelper;
use common\models\StoryTest;
use common\models\User;
use common\models\UserQuestionHistoryModel;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TestMobileController extends BaseController
{

    public function actionView(int $id, int $student_id = null)
    {
        if (($model = StoryTest::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
            'studentId' => $student_id,
        ]);
    }

    public function actionGetData(int $test_id, int $student_id = null, bool $fast_mode = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($model = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $userHistory = [];
        $userStars = [];
        $userStarsCount = 0;
        if ($student_id !== null && !$fast_mode) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $student_id;
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistoryLocal($model->id);
            $userStars = $userQuestionHistoryModel->getUserQuestionHistoryStarsLocal($model->id);
            $userStarsCount = $userQuestionHistoryModel->getUserHistoryStarsCountLocal($model->id);
        }

        $collection = (new TestBuilder($model, $model->getQuestionDataMobile($userHistory), $model->getQuestionDataCountMobile(), $userStars, $fast_mode))
            ->build();
        return (new Serializer())
            ->serialize($model, $collection, [], $userStarsCount, $fast_mode);
    }

    public function actionInit(int $test_id, int $user_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($model = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $user = User::findOne($user_id);
        if ($user === null && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
        }

        return [
            'test' => [
                'header' => $model->header,
                'description' => $model->description_text,
                'remote' => $model->isRemote(),
            ],
            'students' => UserHelper::getUserStudents($model, $user),
        ];
    }
}