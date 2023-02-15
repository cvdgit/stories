<?php

declare(strict_types=1);

namespace frontend\modules\repetition\Finish;

use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class FinishAction extends Action
{
    private $handler;

    public function __construct($id, $controller, FinishHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Response $response, Request $request): array
    {
        $response->format = Response::FORMAT_JSON;

        $finishForm = new FinishForm();

        if ($finishForm->load($request->post(), '')) {

            if (!$finishForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            try {
                $this->handler->handle($finishForm);
                return ['success' => true];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ['success' => false];
            }
        }

        return ['success' => false];
    }
}
