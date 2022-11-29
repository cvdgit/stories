<?php

namespace backend\controllers;

use backend\components\StoryTextFormatter;
use backend\components\WordListFormatter;
use backend\forms\WordListForm;
use backend\forms\WordListPoetryForm;
use backend\models\test\CreateStoryForm;
use backend\models\WordListAsTextForm;
use backend\models\WordListFromStoryForm;
use backend\services\WordListService;
use common\models\Story;
use backend\services\StoryEditorService;
use common\rbac\UserRoles;
use Exception;
use Yii;
use common\models\TestWordList;
use backend\models\TestWordListSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

/**
 * WordListController implements the CRUD actions for TestWordList model.
 */
class WordListController extends Controller
{
    private $storyService;
    private $wordListService;

    public function __construct($id, $module, StoryEditorService $storyService, WordListService $wordListService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->wordListService = $wordListService;
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(Request $request): string
    {
        $searchModel = new TestWordListSearch();
        $dataProvider = $searchModel->search($request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(Request $request)
    {
        $wordListForm = new WordListForm();
        if ($wordListForm->load($request->post()) && $wordListForm->validate()) {
            try {
                $this->wordListService->createWordList($wordListForm);
                Yii::$app->session->setFlash('success', 'Список успешно создан');
                return $this->redirect(['index']);
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('create', [
            'model' => $wordListForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id, Request $request)
    {
        $wordList = $this->findModel($id);
        $wordListForm = new WordListForm($wordList);
        if ($wordListForm->load($request->post()) && $wordListForm->validate()) {
            try {
                $this->wordListService->updateWordList($wordList, $wordListForm);
                Yii::$app->session->setFlash('success', 'Список успешно обновлен');
            } catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        $wordsDataProvider = new ActiveDataProvider([
            'query' => $wordList->getTestWords(),
            'pagination' => false,
        ]);
        return $this->render('update', [
            'model' => $wordListForm,
            'wordsDataProvider' => $wordsDataProvider,
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
     * @throws NotFoundHttpException
     */
    private function findModel(int $id): TestWordList
    {
        if (($model = TestWordList::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Список слов не найден');
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

    public function actionCreateFromText()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new WordListAsTextForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $wordList = $this->findModel($model->word_list_id);
                $model->createWordList();
            }
            catch (Exception $ex) {
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
            catch (Exception $ex) {
                return Json::encode(['success' => false, 'message' => $ex->getMessage()]);
            }
        }
        return $this->renderAjax('_create_story', [
            'model' => $model,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreatePoetry(int $word_list_id, Request $request, Response $response, WebUser $user)
    {
        $wordList = $this->findModel($word_list_id);
        $form = new WordListPoetryForm();
        $form->name = $wordList->name;
        if ($form->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$form->validate()) {
                return ['success' => false, 'message' => 'Validation error'];
            }
            try {
                $this->wordListService->createPoetry($user->getId(), $form, $wordList->testWords);
                return ['success' => true, 'message' => 'OK'];
            } catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('create-poetry-modal', [
            'formModel' => $form,
        ]);
    }
}
