<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Update;

use backend\Testing\Questions\Grouping\Create\CreateGroupingHandler;
use common\models\StoryTestQuestion;
use Exception;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class UpdateGroupingAction extends Action
{
    /**
     * @var CreateGroupingHandler
     */
    private $handler;

    public function __construct($id, $controller, UpdateGroupingHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     * @return string|array
     */
    public function run(int $id, Request $request, Response $response)
    {
        $questionModel = StoryTestQuestion::findOne($id);
        if ($questionModel === null) {
            throw new NotFoundHttpException('Вопрос не найден');
        }

        $quizModel = $questionModel->storyTest;
        $updateForm = new UpdateGroupingForm($questionModel);

        if ($request->isPost) {
            $updateForm = new UpdateGroupingForm();
            if ($updateForm->load($request->post())) {
                $response->format = Response::FORMAT_JSON;
                if (!$updateForm->validate()) {
                    return ["success" => false, "message" => "Not valid"];
                }
                try {
                    $this->handler->handle(new UpdateGroupingCommand($quizModel->id, $questionModel->id, $updateForm->name, $updateForm->payload));
                    return ["success" => true];
                } catch (Exception $exception) {
                    Yii::$app->errorHandler->logException($exception);
                    return ["success" => false, "message" => $exception->getMessage()];
                }
            }
        }

        return $this->controller->render('update', [
            'quizModel' => $quizModel,
            'formModel' => $updateForm,
            "questionModel" => $questionModel,
        ]);
    }
}
