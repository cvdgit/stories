<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\Form;

use common\models\StoryTest;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class FormAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $test_id, Request $request, Response $response)
    {
        $test = StoryTest::findOne($test_id);
        if ($test === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $importForm = new QuestionsImportForm([
            'to_test_id' => $test->id,
        ]);
        return $this->controller->renderAjax('import', [
            'testId' => $test_id,
            'formModel' => $importForm,
        ]);
    }
}
