<?php

namespace backend\controllers\test;

use backend\models\drag_words\CreateDragWordsForm;
use backend\services\DragWordsService;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
    public function actionCreate(int $test_id)
    {
        if (($quizModel = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('Quiz not found');
        }

        $createDragWordsForm = new CreateDragWordsForm();
        if ($this->request->isPost && $createDragWordsForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $this->dragWordsService->create($quizModel->id, $createDragWordsForm);
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return ['success' => true, 'url' => Url::to(['test/update', 'id' => $quizModel->id])];
            }
            catch (\Exception $exception) {
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
    public function actionUpdate(int $id)
    {
        if (($questionModel = StoryTestQuestion::findOne($id)) === null) {
            throw new NotFoundHttpException('Question not found');
        }

        $createDragWordsForm = new CreateDragWordsForm();
        $createDragWordsForm->name = $questionModel->name;
        $createDragWordsForm->payload = $questionModel->regions;

        if ($this->request->isPost && $createDragWordsForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $this->dragWordsService->update($questionModel, $createDragWordsForm);
                return ['success' => true];
            }
            catch (\Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        $quizModel = $questionModel->storyTest;
        return $this->render('update', [
            'quizModel' => $quizModel,
            'model' => $createDragWordsForm,
            'questionModel' => $questionModel,
        ]);
    }
}
