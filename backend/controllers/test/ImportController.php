<?php

namespace backend\controllers\test;

use backend\models\test\import\ImportFromWordList;
use backend\services\ImportQuestionService;
use common\models\StoryTest;
use Exception;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ImportController extends Controller
{

    private $importService;

    public function __construct($id, $module, ImportQuestionService $importService, $config = [])
    {
        $this->importService = $importService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionFromWordList(int $test_id)
    {
        if (($quizModel = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('Тест не найден');
        }
        $importForm = new ImportFromWordList();
        if ($this->request->isPost && $importForm->load($this->request->post())) {
            try {
                $this->importService->importFromWordList($quizModel, $importForm);
                return Json::encode(['success' => true]);
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('_from_word_list', [
            'model' => $importForm,
        ]);
    }
}
