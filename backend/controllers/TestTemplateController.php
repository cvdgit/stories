<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\test_template\CreateTestsForm;
use backend\models\test_template\CreateTestTemplateForm;
use backend\models\test_template\TestItemForm;
use common\models\TestWordList;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TestTemplateController extends BaseController
{

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new CreateTestTemplateForm();
        if ($model->load(Yii::$app->request->post())) {
            try {
                $templateId = $model->create();
                Yii::$app->session->setFlash('success', 'Шаблон успешно создан');
                return ['success' => true, 'url' => Url::to(['test/update', 'id' => $templateId])];
            }
            catch (Exception $ex) {
                return ['success' => false, 'error' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'error' => 'No data'];
    }

    public function actionCreateTests(int $word_list_id)
    {
        if (($wordListModel = TestWordList::findOne($word_list_id)) === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $model = new CreateTestsForm();
        $model->word_list_id = $wordListModel->id;
        $model->story_name = $wordListModel->name;

        $items = [];
        $defaultItems = Yii::$app->params['tests.template'];
        if (!empty($defaultItems)) {
            foreach ($defaultItems as $defaultItem) {
                $testItem = new TestItemForm();
                $testItem->template_id = $defaultItem['template_id'];
                $testItem->word_list_processing = $defaultItem['word_list_processing'];
                $items[] = $testItem;
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            $items = [];
            foreach (Yii::$app->request->post('TestItemForm', []) as $rawModel) {
                $items[] = new TestItemForm();
            }

            if (Model::loadMultiple($items, Yii::$app->request->post()) && Model::validateMultiple($items)) {
                $model->items = $items;
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            try {
                $model->create();
                return ['success' => true];
            }
            catch (Exception $ex) {
                return ['success' => false, 'error' => $ex->getMessage()];
            }
        }

        return $this->renderAjax('_create_from_template', [
            'model' => $model,
            'items' => $items,
        ]);
    }
}
