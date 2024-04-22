<?php

namespace backend\controllers\test;

use backend\models\drag_words\CreateDragWordsForm;
use backend\models\drag_words\UpdateDragWordsForm;
use backend\services\DragWordsService;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class DragWordsController extends Controller
{

    private $dragWordsService;

    public function __construct($id, $module, DragWordsService $dragWordsService, $config = [])
    {
        $this->dragWordsService = $dragWordsService;
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
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $test_id, Request $request, Response $response)
    {
        if (($quizModel = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('Quiz not found');
        }

        $createDragWordsForm = new CreateDragWordsForm();
        if ($createDragWordsForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            $createDragWordsForm->imageFile = UploadedFile::getInstance($createDragWordsForm, "imageFile");
            if (!$createDragWordsForm->validate()) {
                return ["success" => false, "message" => $createDragWordsForm->getErrorSummary(true)];
            }
            try {
                $this->dragWordsService->create($quizModel->id, $createDragWordsForm);
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return ['success' => true, 'url' => Url::to(['test/update', 'id' => $quizModel->id])];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return $this->render('create', [
            'quizModel' => $quizModel,
            'model' => $createDragWordsForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id, Request $request, Response $response)
    {
        if (($questionModel = StoryTestQuestion::findOne($id)) === null) {
            throw new NotFoundHttpException('Question not found');
        }

        $updateDragWordsForm = new UpdateDragWordsForm($questionModel);

        if ($updateDragWordsForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            $updateDragWordsForm->imageFile = UploadedFile::getInstance($updateDragWordsForm, "imageFile");
            if (!$updateDragWordsForm->validate()) {
                return ["success" => false, "message" => $updateDragWordsForm->getErrorSummary(true)];
            }
            try {
                $this->dragWordsService->update($questionModel->id, $updateDragWordsForm);
                return ['success' => true];
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        $quizModel = $questionModel->storyTest;
        return $this->render('update', [
            'quizModel' => $quizModel,
            'model' => $updateDragWordsForm,
            'questionModel' => $questionModel,
        ]);
    }
}
