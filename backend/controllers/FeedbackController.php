<?php

namespace backend\controllers;

use backend\components\FeedbackPathBuilder;
use backend\services\FeedbackService;
use common\models\story_feedback\StoryFeedback;
use Yii;
use yii\filters\AccessControl;
use common\models\story_feedback\StoryFeedbackSearch;
use common\rbac\UserRoles;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeedbackController extends Controller
{

    private $feedbackPathBuilder;
    private $feedbackService;

    public function __construct($id, $module, FeedbackPathBuilder $feedbackPathBuilder, FeedbackService $feedbackService, $config = [])
    {
        $this->feedbackPathBuilder = $feedbackPathBuilder;
        $this->feedbackService = $feedbackService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_FEEDBACK_ACCESS],
                    ],
                ],
            ],
        ];
    }

	public function actionIndex(int $status = 0): string
    {
        $searchModel = new StoryFeedbackSearch();
        $dataProvider = $searchModel->search($status, $this->request->queryParams);
		return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status,
            'builder' => $this->feedbackPathBuilder,
        ]);
	}

/*    public function actionBatchupdate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = ['success' => false];
        $post = Yii::$app->request->post();
        $response['success'] = StoryFeedback::updateStatus($post['data']);
        return $response;
    }*/

    /**
     * @throws NotFoundHttpException
     */
    public function actionSuccess(int $id): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if ((!$feedbackModel = StoryFeedback::findOne($id)) === null) {
            throw new NotFoundHttpException('Запись обратной связи не найдена');
        }
        try {
            $this->feedbackService->success($feedbackModel->id);
            return ['success' => true];
        }
        catch (\Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
