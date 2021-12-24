<?php

namespace frontend\controllers;

use backend\components\BaseController;
use backend\components\training\base\Serializer;
use backend\components\training\collection\TestBuilder;
use common\helpers\UserHelper;
use common\models\StoryTest;
use common\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TestMobileController extends BaseController
{

    public function actionView(int $id)
    {
        if (($model = StoryTest::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionGetData(int $test_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($model = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $collection = (new TestBuilder($model, $model->getQuestionData(), $model->getQuestionDataCount(), []))
            ->build();
        return (new Serializer())
            ->serialize($model, $collection, [], 0);
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