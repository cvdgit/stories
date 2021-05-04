<?php

namespace backend\controllers\test;

use backend\models\test\import\ImportFromWordList;
use Exception;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

class ImportController extends Controller
{

    public function actionFromWordList(int $test_id)
    {
        $model = new ImportFromWordList();
        $model->test_id = $test_id;
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->import();
                return Json::encode(['success' => true]);
            }
            catch (Exception $ex) {
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('_from_word_list', [
            'model' => $model,
        ]);
    }

}