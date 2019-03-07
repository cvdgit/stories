<?php

namespace backend\controllers;

use Yii;
use common\models\Story;
use common\models\StorySearch;
use backend\models\StoryCoverUploadForm;
use backend\models\StoryFileUploadForm;
use backend\models\SourcePowerPointForm;
use backend\models\SourceDropboxForm;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use common\services\StoryService;
use yii\web\UploadedFile;
use yii\web\HttpException;

class StoryController extends \yii\web\Controller
{
    
    public $service;

    public function __construct($id, $module, StoryService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
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
        
        $coverUploadForm = new StoryCoverUploadForm();
        $fileUploadForm = new StoryFileUploadForm();
        
        if ($model->load(Yii::$app->request->post())) {
            
            $coverUploadForm->coverFile = UploadedFile::getInstance($coverUploadForm, 'coverFile');
            if ($coverUploadForm->coverFile !== null) {
                if ($coverUploadForm->upload()) {
                    $model->cover = $coverUploadForm->coverFile;
                }
                else {
                    print_r($coverUploadForm->getErrors());
                }
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
        $searchModel->scenario = StorySearch::SCENARIO_BACKEND;
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

        $dropboxForm = new SourceDropboxForm();
        $dropboxForm->storyId = $model->id;
        $dropboxForm->storyFile = $model->story_file;

        if ($model->load(Yii::$app->request->post())) {

            $coverUploadForm->coverFile = UploadedFile::getInstance($coverUploadForm, 'coverFile');
            if ($coverUploadForm->coverFile !== null) {
                if ($coverUploadForm->upload()) {
                    $model->cover = $coverUploadForm->coverFile;
                }
                else {
                    print_r($coverUploadForm->getErrors());
                }
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

    public function actionImages($id)
    {
        $model = $this->findModel($id);
        return $this->render('images', [
            'model' => $model,
            'images' => $this->service->getStoryImages($model),
        ]);
    }

    public function actionImportFromPowerPoint()
    {
        $model = new SourcePowerPointForm();
        $service = $this->service->getPowerPointSerivce();
        return $this->importStory($model, $service);
    }

    public function actionImportFromDropBox()
    {
        $model = new SourceDropboxForm();
        $serivce = $this->service->getDropboxSerivce();
        return $this->importStory($model, $service);
    }

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

}
