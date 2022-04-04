<?php

namespace backend\controllers;

use backend\models\question\input\CreateInputQuestionForm;
use common\models\StoryTest;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class QuestionInputController extends Controller
{

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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $test_id)
    {
        $testModel = $this->findModel($test_id);
        $form = new CreateInputQuestionForm();
        if ($form->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $form->createQuestion($testModel->id);
                return ['success' => true];
            }
            catch(\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return $this->renderAjax('create', [
            'model' => $form,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findModel($id): ?StoryTest
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}