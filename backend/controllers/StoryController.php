<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
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

    public function __construct($id, $module, StoryService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
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
        $model->loadDefaultValues();
        $model->source_id = Story::SOURCE_POWERPOINT;
        
        $coverUploadForm = new StoryCoverUploadForm();
        $fileUploadForm = new StoryFileUploadForm();
        
        if ($model->load(Yii::$app->request->post())) {
            
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

            $model->save();
            return $this->redirect(['update', 'id' => $model->id]);
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

        if ($model->load(Yii::$app->request->post())) {

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

            $model->save();
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
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new SourcePowerPointForm();
        if ($model->load(Yii::$app->request->post())) {
            $this->service->importStoryFromPowerPoint($model);
        }
        return ['success' => true];
    }

    public function actionImportFromDropBox()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new SourceDropboxForm();
        if ($model->load(Yii::$app->request->post())) {
            $this->service->importStoryFromDropbox($model);
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

    /*
    protected function importStory($model, $service)
    {
        if ($model->load(Yii::$app->request->post())) {
            $body = '';
            $slidesNumber = 0;
            try {

                $story = $service->loadStory($model);
                $slidesNumber = $story->getSlideCount();

                $storyEditor = new \backend\components\StoryEditor($story);
                $html = $storyEditor->getStoryMarkup();

                $body = '<div class="slides">' . $html . '</div>';
                $html = '';
            }
            catch (Exception $ex) {
                return $this->sendErrorResponse($ex->getMessage());
            }
            $model->saveSource($body, $slidesNumber);
            return $this->sendSuccessResponse('Успешно');
        }
        return $this->sendErrorResponse($model->getErrors());
    }

    protected function jsonResponse($result)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->asJson($result);
    }

    protected function sendSuccessResponse($response)
    {
        return $this->jsonResponse(['success' => $response, 'error' => '']);
    }

    protected function sendErrorResponse($response)
    {
        return $this->jsonResponse(['success' => '', 'error' => $response]);
    }
    */

}
