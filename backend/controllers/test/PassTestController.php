<?php

namespace backend\controllers\test;

use backend\models\pass_test\PassTestForm;
use backend\services\PassTestService;
use backend\Testing\Questions\PassTest\ImagesAction;
use backend\Testing\Questions\PassTest\RegionImageUploadAction;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Exception;
use vova07\imperavi\actions\UploadFileAction;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class PassTestController extends Controller
{
    private $passTestService;

    public function __construct($id, $module, PassTestService $passTestService, $config = [])
    {
        $this->passTestService = $passTestService;
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

    public function actions(): array
    {
        return [
            'region-image-upload' => RegionImageUploadAction::class,
            'images' => ImagesAction::class,
            'image-upload' => [
                'class' => UploadFileAction::class,
                'url' => '/upload/pass-test/', // Directory URL address, where files are stored.
                'path' => '@public/upload/pass-test',
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

        $createPassTestForm = new PassTestForm();
        if ($createPassTestForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            $createPassTestForm->imageFile = UploadedFile::getInstance($createPassTestForm, "imageFile");
            if (!$createPassTestForm->validate()) {
                return ["success" => false, "message" => $createPassTestForm->getErrorSummary(true)];
            }
            try {
                $this->passTestService->create($quizModel->id, $createPassTestForm);
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return ['success' => true, 'url' => Url::to(['test/update', 'id' => $quizModel->id])];
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return $this->render('create', [
            'quizModel' => $quizModel,
            'model' => $createPassTestForm,
            'testingId' => $quizModel->id,
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

        $updatePassTestForm = new PassTestForm($questionModel);

        if ($updatePassTestForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            $updatePassTestForm->imageFile = UploadedFile::getInstance($updatePassTestForm, "imageFile");
            if (!$updatePassTestForm->validate()) {
                return ["success" => false, "message" => $updatePassTestForm->getErrorSummary(true)];
            }
            try {
                $this->passTestService->update($questionModel->id, $updatePassTestForm);
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
            'model' => $updatePassTestForm,
            'questionModel' => $questionModel,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionPayload(int $id, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $question = StoryTestQuestion::findOne($id);
        if ($question === null) {
            throw new NotFoundHttpException('Вопрос не найден');
        }
        return Json::decode($question->regions);
    }
}
