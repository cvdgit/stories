<?php

namespace backend\controllers\test;

use backend\models\question\sequence\CreateSequenceQuestion;
use backend\models\question\sequence\UpdateSequenceQuestion;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class QuestionSequenceController extends Controller
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

    public function actionCreate(int $test_id)
    {
        $testModel = $this->findTestModel($test_id);
        $model = new CreateSequenceQuestion($test_id);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->createQuestion();
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
        $form = new UpdateSequenceQuestion($model);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $form->updateQuestion();
                Yii::$app->session->setFlash('success', 'Вопрос успешно изменен');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $form,
            'testModel' => $model->storyTest,
            'errorText' => $model->getAnswersErrorText(),
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['test/update', 'id' => $model->story_test_id]);
    }

    protected function findModel($id)
    {
        if (($model = StoryTestQuestion::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findTestModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}