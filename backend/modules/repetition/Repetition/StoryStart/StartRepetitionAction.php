<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\StoryStart;

use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class StartRepetitionAction extends Action
{
    private $handler;

    public function __construct($id, $controller, StartRepetitionHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $startForm = new StartRepetitionForm();
        if ($startForm->load($request->post(), '')) {
            if (!$startForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            try {
                $this->handler->handle($startForm);
                return ['success' => true, 'test_id' => $startForm->test_id];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
