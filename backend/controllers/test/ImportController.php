<?php

namespace backend\controllers\test;

use backend\models\question\QuestionType;
use backend\models\test\import\ImportFromWordList;
use backend\services\ImportQuestionService;
use common\models\StoryTest;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class ImportController extends Controller
{
    /** @var ImportQuestionService */
    private $importService;

    public function __construct($id, $module, ImportQuestionService $importService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->importService = $importService;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionFromWordList(int $test_id, Request $request, Response $response)
    {
        if (($quizModel = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('Тест не найден');
        }
        $importForm = new ImportFromWordList();
        if ($request->isPost && $importForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$importForm->validate()) {
                return ['success' => false, 'message' => implode(PHP_EOL, $importForm->getErrorSummary(true))];
            }

            try {

                switch ((int)$importForm->question_type) {
                    case QuestionType::ONE:
                        $this->importService->processDefault($quizModel->id, $importForm);
                        break;
                    case QuestionType::SEQUENCE:
                        $this->importService->processSequence($quizModel->id, $importForm);
                        break;
                    case QuestionType::POETRY:
                        $this->importService->processPoetry($quizModel->id, $importForm);
                        break;
                    default:
                        return ['success' => false, 'message' => 'Unknown question type'];
                }

                return ['success' => true];
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'errors' => [$ex->getMessage()]];
            }
        }
        return $this->renderAjax('_from_word_list', [
            'model' => $importForm,
        ]);
    }
}
