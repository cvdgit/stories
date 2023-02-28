<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingCreate;

use backend\modules\repetition\Repetition\UserStudentItemsFetcher;
use backend\modules\repetition\Schedule\Schedule;
use common\models\StoryTest;
use common\models\UserStudent;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class CreateAction extends Action
{
    private $handler;

    public function __construct($id, $controller, CreateRepetitionHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     * @return array|string
     */
    public function run(int $test_id, Request $request, Response $response)
    {
        $testing = StoryTest::findOne($test_id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }
        $createForm = new CreateRepetitionForm([
            'test_id' => $testing->id,
            'test_name' => $testing->header,
        ]);

        if ($createForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            try {
                $this->handler->handle($createForm);
                return ['success' => true, 'message' => 'Успешно'];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return $this->controller->renderAjax('create', [
            'formModel' => $createForm,
            'studentItems' => ArrayHelper::map((new UserStudentItemsFetcher())->fetch(), 'studentId', 'studentName'),
            'scheduleItems' => ArrayHelper::map(Schedule::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        ]);
    }
}
