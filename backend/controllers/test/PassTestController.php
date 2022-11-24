<?php

namespace backend\controllers\test;

use backend\models\pass_test\PassTestForm;
use backend\services\PassTestService;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $test_id)
    {
        if (($quizModel = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('Quiz not found');
        }

        $createPassTestForm = new PassTestForm();
        if ($this->request->isPost && $createPassTestForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $this->passTestService->create($quizModel->id, $createPassTestForm);
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return ['success' => true, 'url' => Url::to(['test/update', 'id' => $quizModel->id])];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return $this->render('create', [
            'quizModel' => $quizModel,
            'model' => $createPassTestForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        if (($questionModel = StoryTestQuestion::findOne($id)) === null) {
            throw new NotFoundHttpException('Question not found');
        }

        $createPassTestForm = new PassTestForm($questionModel);

        if ($this->request->isPost && $createPassTestForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $this->passTestService->update($questionModel, $createPassTestForm);
                return ['success' => true];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        $quizModel = $questionModel->storyTest;
        return $this->render('update', [
            'quizModel' => $quizModel,
            'model' => $createPassTestForm,
            'questionModel' => $questionModel,
        ]);
    }
}
