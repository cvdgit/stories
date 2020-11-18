<?php

namespace backend\controllers;

use backend\models\question\CreateRegionQuestion;
use backend\models\question\UpdateRegionQuestion;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        $model = new CreateRegionQuestion();
        $model->test_id = $test_id;
        $model->type = $type;
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->create();
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        return $this->render('create', [
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

    protected function findModel($id)
    {
        if (($model = StoryTestQuestion::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreateAnswers(int $question_id)
    {
        $model = $this->findModel($question_id);
        $regions = Json::decode($model->regions);
        foreach ($regions as $region) {
            $answer = StoryTestAnswer::create($model->id, $region['title'], $region['correct']);
            $answer->region_id = $region['id'];
            $answer->save();
        }
        Yii::$app->session->setFlash('success', 'Успешно');
        return $this->redirect(['update', 'id' => $question_id]);
    }

}