<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Update;

use common\models\StoryTestQuestion;
use Exception;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class UpdateAction extends Action
{
    /**
     * @var UpdateHandler
     */
    private $handler;

    public function __construct($id, $controller, UpdateHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(int $id, Request $request, Response $response)
    {
        $questionModel = StoryTestQuestion::findOne($id);
        if ($questionModel === null) {
            throw new NotFoundHttpException('Question not found');
        }

        $updateForm = new UpdateImageGapsForm($questionModel);
        $imageUrl = '/test_images/image_gaps/' . $questionModel->image;

        if (!file_exists(\Yii::getAlias('@public') . $imageUrl)) {
            throw new NotFoundHttpException('Question image not found');
        }

        if ($updateForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            try {
                $this->handler->handle(new UpdateCommand($questionModel->id, $updateForm->name, $updateForm->payload, (int) $updateForm->max_prev_items));
                return ['success' => true];
            }
            catch (Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        [$width, $height] = getimagesize(\Yii::getAlias('@public') . $imageUrl);

        $quizModel = $questionModel->storyTest;
        return $this->controller->render('update', [
            'quizModel' => $quizModel,
            'formModel' => $updateForm,
            'questionModel' => $questionModel,
            'imageParams' => [
                'url' => $imageUrl,
                'width' => $width,
                'height' => $height,
            ],
        ]);
    }
}
