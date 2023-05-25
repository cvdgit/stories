<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\ManageTopics;

use yii\base\Action;
use yii\base\Model;
use yii\web\Request;
use yii\web\Response;

class TopicAccessAction extends Action
{
    private $handler;

    public function __construct($id, $controller, TopicAccessHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $accessForm = new ManageTopicForm();
        if ($accessForm->load($request->post())) {
            if (!$accessForm->validate()) {
                return ['success' => false, 'message' => 'not valid'];
            }

            $items = [];
            foreach ($request->post('TopicAccessForm', []) as $i => $rawModel) {
                $items[$i] = new TopicAccessForm();
            }
            if (count($items) === 0 || (Model::loadMultiple($items, $request->post()) && Model::validateMultiple($items))) {
                try {
                    $this->handler->handle(new TopicAccessCommand((int) $accessForm->class_book_id, $items));
                    return ['success' => true, 'message' => 'success'];
                } catch (\Exception $exception) {
                    \Yii::$app->errorHandler->logException($exception);
                    return ['success' => false, 'message' => $exception->getMessage()];
                }
            } else {
                return ['success' => false, 'message' => 'items not valid'];
            }
        }
        return ['success' => false, 'message' => 'no data'];
    }
}
