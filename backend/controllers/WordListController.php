<?php

namespace backend\controllers;

use backend\components\StoryTextFormatter;
use backend\models\WordListFromStoryForm;
use common\models\Story;
use backend\services\StoryEditorService;
use common\rbac\UserRoles;
use Yii;
use common\models\TestWordList;
use backend\models\TestWordListSearch;
use yii\filters\AccessControl;
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

    public function __construct($id, $module, StoryEditorService $storyService, $config = [])
    {
        $this->storyService = $storyService;
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
        $model = new TestWordList();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Список успешно создан');
            return $this->redirect(['update', 'id' => $model->id]);
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
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Список успешно обновлен');
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
            catch (Exception $ex) {
                return ['message' => $ex->getMessage()];
            }
            return $this->redirect(['word-list/update', 'id' => $wordListID]);
        }
        return ['message' => 'Error'];
    }

}
