<?php

namespace backend\controllers;

use backend\models\question\CreateRegionQuestion;
use backend\models\question\UpdateRegionQuestion;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class QuestionController extends Controller
{

    public function behaviors()
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

    public function actionCreate(int $test_id, int $type)
    {
        $testModel = $this->findTestModel($test_id);
        $model = new CreateRegionQuestion();
        $model->test_id = $test_id;
        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->create();
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        return $this->render('create', [
            'testModel' => $testModel,
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $form = new UpdateRegionQuestion($model);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $form->update();
                Yii::$app->session->setFlash('success', 'Вопрос успешно изменен');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $form,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['test/update', 'id' => $model->story_test_id]);
    }

    private function findModel($id)
    {
        if (($model = StoryTestQuestion::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function findAnswerModel($id)
    {
        if (($model = StoryTestAnswer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function findTestModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteAnswer($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findAnswerModel($id);
        $model->delete();
        return ['success' => true];
    }

    public function actionDeleteImage(int $id)
    {
        $model = $this->findModel($id);
        $fileDeleted = false;
        try {
            $model->deleteImage();
            $fileDeleted = true;
        }
        catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }

        $model->image = null;
        $model->save();
        Yii::$app->session->setFlash('success', 'Изображение успешно удалено');

        return $this->redirect(['test/update-question', 'question_id' => $model->id]);
    }

}