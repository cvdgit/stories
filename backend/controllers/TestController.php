<?php

namespace backend\controllers;

use backend\models\AnswerImageUploadForm;
use backend\models\question\CreateQuestion;
use backend\models\question\UpdateQuestion;
use backend\models\search\TestSearch;
use backend\models\test\ChangeRepeatForm;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\models\StoryTestResult;
use common\models\test\AnswerType;
use common\models\test\SourceType;
use common\models\test\TestStatus;
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

    public function actionTemplates()
    {
        $searchModel = new TestSearch();
        $params = array_merge([], Yii::$app->request->queryParams);
        $params['TestSearch']['status'] = TestStatus::TEMPLATE;
        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'source' => 123,
            'sourceRecordsTotal' => 0,
        ]);
    }

    public function actionCreate(int $source)
    {
        $model = new StoryTest();
        $model->source = $source;
        $model->created_by = Yii::$app->user->id;
        $model->answer_type = AnswerType::DEFAULT;
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
            'repeatChangeModel' => new ChangeRepeatForm($model->id, $model->repeat),
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
        $model = new CreateQuestion($testModel->id);

        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->create();
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return $this->redirect(['test/update-question', 'question_id' => $id]);
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }

        return $this->render('question_create', [
            'testModel' => $testModel,
            'model' => $model,
            'dataProvider' => null,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdateQuestion(int $question_id)
    {
        if (($question = StoryTestQuestion::findOne($question_id)) === null) {
            throw new NotFoundHttpException('Question not found');
        }

        if (($route = $question->getQuestionUpdateRoute()) !== null) {
            return $this->redirect($route);
        }

        $model = new UpdateQuestion($question);
        if ($model->load(Yii::$app->request->post())) {
            $model->update();
            $action = Yii::$app->request->post('action');
            if ($action === 'save') {
                Yii::$app->session->addFlash('success', 'Вопрос успешно сохранен');
                return $this->refresh();
            }
            return $this->redirect(['test/update', 'id' => $model->story_test_id]);
        }

        $testModel = $question->storyTest;

        return $this->render('question_update', [
            'model' => $model,
            'dataProvider' => $model->getAnswersDataProvider(),
            'testModel' => $testModel,
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
            $action = Yii::$app->request->post('action');
            if ($action === 'save') {
                Yii::$app->session->addFlash('success', 'Ответ успешно сохранен');
                return $this->redirect(['test/update-answer', 'answer_id' => $model->id]);
            }
            if ($action === 'save-and-create') {
                Yii::$app->session->addFlash('success', 'Ответ успешно создан');
                return $this->redirect(['test/create-answer', 'question_id' => $questionModel->id]);
            }
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
            $action = Yii::$app->request->post('action');
            if ($action === 'save') {
                Yii::$app->session->addFlash('success', 'Ответ успешно сохранен');
                return $this->refresh();
            }
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionChangeRepeat(int $test_id): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $testModel = $this->findModel($test_id);
        $model = new ChangeRepeatForm($testModel->id);
        if ($model->load($this->request->post())) {
            try {
                $this->historyService->clearTestHistory($testModel->id);
                $repeat = $model->updateRepeat();
                return ['success' => true, 'message' => 'Успешно', 'repeat' => $repeat];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
