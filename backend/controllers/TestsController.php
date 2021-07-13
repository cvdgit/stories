<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\test\RelatedTestsForm;
use common\models\StoryTest;
use Yii;
use yii\web\Response;

class TestsController extends BaseController
{

    public function actionManage(int $test_id)
    {
        $testModel = $this->findModel(StoryTest::class, $test_id);
        return $this->renderAjax('manage', [
            'testModel' => $testModel,
        ]);
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new RelatedTestsForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var StoryTest $testModel */
            $testModel = $this->findModel(StoryTest::class, $model->test_id);
            $model->create($testModel);
            return ['success' => true, 'tests' => $testModel->relatedTests];
        }
        return ['success' => false];
    }
}