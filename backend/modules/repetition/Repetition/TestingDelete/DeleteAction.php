<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingDelete;

use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class DeleteAction extends Action
{
    private $handler;

    public function __construct($id, $controller, DeleteHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(int $test_id, int $student_id, int $schedule_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $deleteForm = new DeleteRepetitionForm([
            'test_id' => $test_id,
            'student_id' => $student_id,
            'schedule_id' => $schedule_id,
        ]);
        if (!$deleteForm->validate()) {
            return ['success' => false, 'message' => 'No valid'];
        }

        try {
            $this->handler->handle($deleteForm);
            return ['success' => true, 'message' => 'Успешно'];
        } catch (\Exception $exception) {
            \Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
