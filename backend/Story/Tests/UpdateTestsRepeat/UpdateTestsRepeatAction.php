<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdateTestsRepeat;

use Exception;
use Yii;
use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class UpdateTestsRepeatAction extends Action
{
    /**
     * @var UpdateTestsRepeatHandler
     */
    private $handler;

    public function __construct($id, $controller, UpdateTestsRepeatHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $form = new UpdateTestsRepeatForm();
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
