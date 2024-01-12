<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdatePassTestsRepeat;

use Exception;
use Yii;
use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class UpdatePassTestsRepeatAction extends Action
{
    /**
     * @var UpdatePassTestsRepeatHandler
     */
    private $handler;

    public function __construct($id, $controller, UpdatePassTestsRepeatHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $form = new UpdatePassTestsRepeatForm();
        if ($form->load($request->post())) {
            if (!$form->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            try {
                $this->handler->handle($form);
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ["success" => true];
    }
}
