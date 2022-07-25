<?php

namespace frontend\controllers;

use common\models\StorySlide;
use Exception;
use frontend\models\feedback\CreateFeedbackForm;
use frontend\services\FeedbackService;
use Yii;
use common\models\Story;
use common\models\story_feedback\StoryFeedback;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeedbackController extends Controller
{

    private $feedbackService;

    public function __construct($id, $module, FeedbackService $feedbackService, $config = [])
    {
        $this->feedbackService = $feedbackService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => AjaxFilter::class,
                'only' => ['create'],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate($id): array
    {
		$this->response->format = Response::FORMAT_JSON;

        if (($storyModel = Story::findOne($id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $createFeedbackForm = new CreateFeedbackForm($storyModel->id);
        if ($this->request->isPost && $createFeedbackForm->load($this->request->post(), '')) {
            try {
                $this->feedbackService->create($createFeedbackForm);
                return ['success' => true];
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false];
            }
        }

        return ['success' => false];
	}
}
