<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Create;

use common\models\StoryTest;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class CreateGroupingAction extends Action
{
    /**
     * @var CreateGroupingHandler
     */
    private $handler;

    public function __construct($id, $controller, CreateGroupingHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     * @return string|array
     */
    public function run(int $test_id, Request $request, Response $response)
    {
        $quizModel = StoryTest::findOne($test_id);
        if ($quizModel === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $createForm = new CreateGroupingForm();
        $createForm->name = 'Сгруппируйте элементы';
        $createForm->payload = Json::encode([
            "groups" => [
                [
                    "id" => Uuid::uuid4()->toString(),
                    "title" => "Группа 1",
                    "items" => [
                        [
                            "id" => Uuid::uuid4()->toString(),
                            "title" => "Группа 1 Элемент 1",
                        ],
                        [
                            "id" => Uuid::uuid4()->toString(),
                            "title" => "Группа 1 Элемент 2",
                        ],
                    ],
                ],
                [
                    "id" => Uuid::uuid4()->toString(),
                    "title" => "Группа 2",
                    "items" => [
                        [
                            "id" => Uuid::uuid4()->toString(),
                            "title" => "Группа 2 Элемент 1",
                        ],
                        [
                            "id" => Uuid::uuid4()->toString(),
                            "title" => "Группа 2 Элемент 2",
                        ],
                    ],
                ]
            ]
        ]);

        if ($createForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$createForm->validate()) {
                return ["success" => false, "message" => "Not valid"];
            }
            try {
                $this->handler->handle(new CreateGroupingCommand($quizModel->id, $createForm->name, $createForm->payload));
                return ["success" => true, 'url' => Url::to(['test/update', 'id' => $quizModel->id])];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ["success" => false, "message" => $exception->getMessage()];
            }
        }

        return $this->controller->render('create', [
            'quizModel' => $quizModel,
            'formModel' => $createForm,
        ]);
    }
}
