<?php


namespace backend\controllers;


use backend\models\AnswerImageUploadForm;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\models\StoryTestResult;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class TestController extends Controller
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

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StoryTest::find(),
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new StoryTest();
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getStoryTestQuestions(),
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getStoryTestQuestions(),
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Тест успешно обновлен');
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreateQuestion(int $test_id)
    {
        $testModel = $this->findModel($test_id);
        $model = new StoryTestQuestion();
        $model->story_test_id = $testModel->id;
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getStoryTestAnswers(),
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['test/update-question', 'question_id' => $model->id]);
        }
        return $this->render('create_question', [
            'testModel' => $testModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateQuestion(int $question_id)
    {
        $model = StoryTestQuestion::findModel($question_id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getStoryTestAnswers(),
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['test/update', 'id' => $model->story_test_id]);
        }
        return $this->render('update_question', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateAnswer(int $question_id)
    {
        $questionModel = StoryTestQuestion::findModel($question_id);
        $model = new StoryTestAnswer();
        $model->story_question_id = $questionModel->id;
        $answerImageModel = new AnswerImageUploadForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $answerImageModel->answerImage = UploadedFile::getInstance($answerImageModel, 'answerImage');
            if ($answerImageModel->upload()) {
                $model->image = $answerImageModel->answerImage;
            }
            $model->save();
            return $this->redirect(['test/update-question', 'question_id' => $questionModel->id]);
        }
        return $this->render('create_answer', [
            'questionModel' => $questionModel,
            'model' => $model,
            'answerImageModel' => $answerImageModel,
        ]);
    }

    public function actionUpdateAnswer(int $answer_id)
    {
        $model = StoryTestAnswer::findModel($answer_id);
        $answerImageModel = new AnswerImageUploadForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $answerImageModel->answerImage = UploadedFile::getInstance($answerImageModel, 'answerImage');
            if ($answerImageModel->upload()) {
                $model->image = $answerImageModel->answerImage;
            }
            $model->save();
            return $this->redirect(['test/update-question', 'question_id' => $model->story_question_id]);
        }
        return $this->render('update_answer', [
            'model' => $model,
            'answerImageModel' => $answerImageModel,
        ]);
    }

    public function actionResults()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StoryTestResult::find()->with(['question', 'user', 'story']),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->render('results', [
            'dataProvider' => $dataProvider,
        ]);
    }

}