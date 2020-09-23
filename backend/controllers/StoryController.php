<?php

namespace backend\controllers;

use backend\components\book\BookStoryGenerator;
use backend\models\StoryBatchCommandForm;
use backend\services\StoryEditorService;
use Exception;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use common\models\Story;
use backend\models\StorySearch;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\models\StoryCoverUploadForm;
use backend\models\StoryFileUploadForm;
use backend\models\SourcePowerPointForm;
use backend\models\SourceDropboxForm;

class StoryController extends Controller
{
    
    public $service;
    protected $editorService;
    private $bookStoryGenerator;

    public function __construct($id,
                                $module,
                                StoryService $service,
                                StoryEditorService $editorService,
                                BookStoryGenerator $bookStoryGenerator,
                                $config = [])
    {
        $this->service = $service;
        $this->editorService = $editorService;
        $this->bookStoryGenerator = $bookStoryGenerator;
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
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new Story model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Story();
        $model->user_id = Yii::$app->user->getId();
        $model->loadDefaultValues();
        $model->source_id = Story::SOURCE_POWERPOINT;
        $model->category_id = 1;
        
        $coverUploadForm = new StoryCoverUploadForm();
        $fileUploadForm = new StoryFileUploadForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $coverUploadForm->coverFile = UploadedFile::getInstance($coverUploadForm, 'coverFile');
            if ($coverUploadForm->coverFile !== null) {
                $model->cover = $coverUploadForm->upload($model->cover);
            }

            if ($model->source_id == Story::SOURCE_SLIDESCOM) {
                $model->story_file = $model->source_dropbox;
            }

            if ($model->source_id == Story::SOURCE_POWERPOINT) {
                $fileUploadForm->storyFile = UploadedFile::getInstance($fileUploadForm, 'storyFile');
                if ($fileUploadForm->storyFile !== null) {
                    if ($fileUploadForm->upload()) {
                        $model->story_file = $fileUploadForm->storyFile;
                    }
                    else {
                        print_r($fileUploadForm->getErrors());
                    }
                }
            }

            $model->categories = explode(',', $model->story_categories);
            if ($model->story_playlists) {
                $model->playlists = explode(',', $model->story_playlists);
            }

            $model->save(false);
            return $this->redirect(['update', 'id' => $model->id]);
        }

        foreach ($model->getErrors() as $error) {
            Yii::$app->session->setFlash('error', $error);
        }

        return $this->render('create', [
            'model' => $model,
            'coverUploadForm' => $coverUploadForm,
            'fileUploadForm' => $fileUploadForm,
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Story model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->fillStoryCategories();
        $model->fillStoryPlaylists();

        if ($model->source_id == Story::SOURCE_SLIDESCOM) {
            $model->source_dropbox = $model->story_file;
        }
        if ($model->source_id == Story::SOURCE_POWERPOINT) {
            $model->source_powerpoint = $model->story_file;
        }

        $coverUploadForm = new StoryCoverUploadForm();
        $fileUploadForm = new StoryFileUploadForm();

        $powerPointForm = new SourcePowerPointForm();
        $powerPointForm->storyId = $model->id;
        $powerPointForm->storyFile = $model->story_file;
        $powerPointForm->slidesNumber = $model->slides_number;

        $dropboxForm = new SourceDropboxForm();
        $dropboxForm->storyId = $model->id;
        $dropboxForm->storyFile = $model->story_file;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $coverUploadForm->coverFile = UploadedFile::getInstance($coverUploadForm, 'coverFile');
            if ($coverUploadForm->coverFile !== null) {
                $model->cover = $coverUploadForm->upload($model->cover);
            }

            if ($model->source_id == Story::SOURCE_SLIDESCOM) {
                $model->story_file = $model->source_dropbox;
            }

            if ($model->source_id == Story::SOURCE_POWERPOINT) {
                $fileUploadForm->storyFile = UploadedFile::getInstance($fileUploadForm, 'storyFile');
                if ($fileUploadForm->storyFile !== null) {
                    if ($fileUploadForm->upload()) {
                        $model->story_file = $fileUploadForm->storyFile;
                    }
                    else {
                        print_r($fileUploadForm->getErrors());
                    }
                }
            }

            $model->categories = explode(',', $model->story_categories);

            if ($model->story_playlists) {
                $model->playlists = explode(',', $model->story_playlists);
            }

            $model->save(false);
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены');

            $powerPointForm->storyFile = $model->story_file;
            $dropboxForm->storyFile = $model->story_file;
        }

        return $this->render('update', [
            'model' => $model,
            'coverUploadForm' => $coverUploadForm,
            'fileUploadForm' => $fileUploadForm,
            'powerPointForm' => $powerPointForm,
            'dropboxForm' => $dropboxForm,
        ]);
    }

    /**
     * Deletes an existing Story model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $this->service->deleteStoryFiles($model);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    public function actionImportFromPowerPoint()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SourcePowerPointForm();
        if ($model->load(Yii::$app->request->post())) {
            $this->service->importStoryFromPowerPoint($model);
        }
        return ['success' => true];
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        // TODO: перенести определение пути до файла в сервис
        $file = Yii::getAlias('@public') . '/slides_file/' . $model->story_file;
        if (file_exists($file)) {
            Yii::$app->response->sendFile($file);
        }
    }

    public function actionBatch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new StoryBatchCommandForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->command === 'AccessBySubscription') {
                Yii::$app->db
                    ->createCommand()
                    ->update('{{%story}}', ['sub_access' => 1],'id IN (' . $model->story_ids . ')')
                    ->execute();
            }

            if ($model->command === 'AccessFree') {
                Yii::$app->db
                    ->createCommand()
                    ->update('{{%story}}', ['sub_access' => 0],'id IN (' . $model->story_ids . ')')
                    ->execute();
            }

            return ['success' => true];
        }
        return ['success' => false];
    }

    public function actionPublish($id)
    {
        $model = Story::findModel($id);
        try {
            $this->service->publishStory($model);
            Yii::$app->session->setFlash('success', 'История опубликована');
        }
        catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка публикации: ' . $e->getMessage());
        }
        return $this->redirect(['update', 'id' => $model->id]);
    }

    public function actionUnpublish($id)
    {
        $model = Story::findModel($id);
        $this->service->unPublishStory($model);
        Yii::$app->session->setFlash('success', 'История снята с публикации');
        return $this->redirect(['update', 'id' => $model->id]);
    }

    public function actionReadonly(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($id);
        $html = $this->bookStoryGenerator->generate($model);
        $model->body = $html;
        $model->save(false, ['body']);
        return ['success' => true];
    }

    public function actionText(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($id);
        $text = $this->editorService->textFromStory($model);
        Yii::$app->response->sendContentAsFile($text, $model->alias. '.txt');
    }

    public function actionAutocomplite(string $query)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return (new Query())
            ->select(['title', 'id', "CONCAT('/slides_cover/list/', cover) AS cover"])
            ->from(Story::tableName())
            ->where(['like', 'title', $query])
            ->orderBy(['title' => SORT_ASC])
            ->limit(30)
            ->all();
    }

}
