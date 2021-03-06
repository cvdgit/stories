<?php

namespace backend\controllers;

use backend\models\AnswerImageUploadForm;
use backend\models\question\CreateQuestion;
use backend\models\question\UpdateQuestion;
use backend\models\search\TestSearch;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\models\StoryTestResult;
use common\models\test\SourceType;
use common\rbac\UserRoles;
use common\services\TestHistoryService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class TestController extends Controller
{

    private $historyService;

    public function __construct($id, $module, TestHistoryService $historyService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->historyService = $historyService;
    }

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

    public function actionIndex(int $source = SourceType::TEST)
    {
        $searchModel = new TestSearch();
        $params = array_merge([], Yii::$app->request->queryParams);
        $params['TestSearch']['source'] = $source;
        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'source' => $source,
            'sourceRecordsTotal' => $this->historyService->getRecordsCountBySource($source),
        ]);
    }

    public function actionCreate(int $source)
    {
        $model = new StoryTest();
        $model->source = $source;
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
            'pagination' => false,
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
        $model = new CreateQuestion();
        $model->story_test_id = $testModel->id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $id = $model->create();
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return $this->redirect(['test/update-question', 'question_id' => $id]);
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        return $this->render('create_question', [
            'testModel' => $testModel,
            'model' => $model,
            'dataProvider' => null,
        ]);
    }

    public function actionUpdateQuestion(int $question_id)
    {
        $question = StoryTestQuestion::findModel($question_id);

        if ($question->typeIsRegion()) {
            return $this->redirect(['question/update', 'id' => $question->id]);
        }
        if ($question->typeIsSequence()) {
            return $this->redirect(['test/question-sequence/update', 'id' => $question->id]);
        }

        $model = new UpdateQuestion($question);
        if ($model->load(Yii::$app->request->post())) {
            $model->update();
            return $this->redirect(['test/update', 'id' => $model->story_test_id]);
        }
        return $this->render('update_question', [
            'model' => $model,
            'dataProvider' => $model->getAnswersDataProvider(),
            'testModel' => $question->storyTest,
            'errorText' => $question->getAnswersErrorText(),
        ]);
    }

    public function actionDeleteQuestion(int $question_id)
    {
        $model = StoryTestQuestion::findModel($question_id);
        $model->delete();
        return $this->redirect(['update', 'id' => $model->story_test_id]);
    }

    public function actionCreateAnswer(int $question_id)
    {
        $questionModel = StoryTestQuestion::findModel($question_id);
        $model = new StoryTestAnswer();
        $model->story_question_id = $questionModel->id;
        $answerImageModel = new AnswerImageUploadForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $answerImageModel->answerImage = UploadedFile::getInstance($answerImageModel, 'answerImage');
            if ($answerImageModel->upload($model->image)) {
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
            if ($answerImageModel->upload($model->image)) {
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

    public function actionDeleteAnswer(int $answer_id)
    {
        $model = StoryTestAnswer::findModel($answer_id);
        $model->delete();
        return $this->redirect(['update-question', 'question_id' => $model->story_question_id]);
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

    public function actionImportAnswers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new ImportAnswersForm();
        $result = ['success' => true, 'result' => [], 'error' => ''];
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            $form->createAnswers();
        }
        else {
            $result['success'] = false;
            $result['error'] = $form->errors;
        }
        return $result;
    }

}