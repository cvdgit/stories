<?php

namespace backend\controllers;

use backend\components\StoryTextFormatter;
use backend\components\WordListFormatter;
use backend\forms\CreateWordList;
use backend\forms\UpdateWordList;
use backend\models\test\CreateStoryForm;
use backend\models\WordListAsTextForm;
use backend\models\WordListFromStoryForm;
use backend\services\WordListService;
use common\models\Story;
use backend\services\StoryEditorService;
use common\rbac\UserRoles;
use Yii;
use common\models\TestWordList;
use backend\models\TestWordListSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * WordListController implements the CRUD actions for TestWordList model.
 */
class WordListController extends Controller
{

    private $storyService;
    private $wordListService;

    public function __construct($id, $module, StoryEditorService $storyService, WordListService $wordListService, $config = [])
    {
        $this->storyService = $storyService;
        $this->wordListService = $wordListService;
        parent::__construct($id, $module, $config);
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

    /**
     * Lists all TestWordList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TestWordListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TestWordList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CreateWordList();
        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->createWordList();
                Yii::$app->session->setFlash('success', 'Список успешно создан');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TestWordList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new UpdateWordList($this->findModel($id));
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->updateWordList();
                Yii::$app->session->setFlash('success', 'Список успешно обновлен');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TestWordList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TestWordList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TestWordList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TestWordList::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findStoryModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionMakeFromStoryByProposals(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $model = $this->findStoryModel($story_id);
        $texts = $this->storyService->textFromStory($model);
        return (new StoryTextFormatter($texts))->formatByProposals();
    }

    public function actionMakeFromStoryByWords(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $model = $this->findStoryModel($story_id);
        $texts = $this->storyService->textFromStory($model);
        return (new StoryTextFormatter($texts))->formatByWords();
    }

    public function actionCreateFromStory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new WordListFromStoryForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $storyModel = $this->findStoryModel($model->story_id);
                $wordListID = $model->createWordList($storyModel);
            }
            catch (\Exception $ex) {
                return ['message' => $ex->getMessage()];
            }
            return $this->redirect(['word-list/update', 'id' => $wordListID]);
        }
        return ['message' => 'Error'];
    }

    public function actionCreateFromText()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new WordListAsTextForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $wordList = $this->findModel($model->word_list_id);
                $model->createWordList();
            }
            catch (\Exception $ex) {
                return ['message' => $ex->getMessage(), 'success' => false];
            }
            return ['message' => 'OK', 'success' => true, 'params' => $wordList->getTestWordsAsArray()];
        }
        return ['message' => 'Error', 'success' => false];
    }

    public function actionTextEdit(int $word_list_id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $model = $this->findModel($word_list_id);
        return WordListFormatter::asText($model->getTestWordsAsArray());
    }

    public function actionCreateStoryForm(int $id)
    {
        $wordListModel = $this->findModel($id);
        $model = new CreateStoryForm($wordListModel);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->create();
                $this->wordListService->create($model, Yii::$app->user->id);
                return Json::encode(['success' => true, 'message' => '']);
            }
            catch (\Exception $ex) {
                return Json::encode(['success' => false, 'message' => $ex->getMessage()]);
            }
        }
        return $this->renderAjax('_create_story', [
            'model' => $model,
        ]);
    }

}
