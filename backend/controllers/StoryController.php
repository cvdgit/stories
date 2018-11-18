<?php

namespace backend\controllers;

use Yii;
use common\models\Story;
use common\models\StorySearch;
use backend\models\StoryCoverUploadForm;
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

            $model->save();
            return $this->redirect(['update', 'id' => $model->id]);
        }
        
        return $this->render('create', [
            'model' => $model,
            'coverUploadForm' => $coverUploadForm,
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
        $coverUploadForm = new StoryCoverUploadForm();

        if (!$model->isDropboxSync()) {
            Yii::$app->session->setFlash('warning', 'Необходимо синхронизировать историю с dropbox');
        }

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

            $model->save();
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены');
        }

        return $this->render('update', [
            'model' => $model,
            'coverUploadForm' => $coverUploadForm,
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
        $this->findModel($id)->delete();

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

    public function actionGetfromdropbox($id)
    {
        $story = $this->findModel($id);
        
        $result = ['success' => '', 'error' => ''];
        if (empty($story->dropbox_story_filename)) {
            $result['error'] = 'Необходимо указать имя файла в Dropbox';
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $this->asJson($result);
        }
        
        try {
            $dropboxSerivce = $this->service->getDropboxSerivce();
            $dropboxSerivce->exportSlideImagesFromDropBox($story->dropbox_story_filename);
            $body = $dropboxSerivce->exportSlideBodyFromDropBox($story->dropbox_story_filename);
            $story->syncWithDropbox($body);
            $story->save(false, ['body', 'dropbox_sync_date']);
            $result['success'] = 'Успешно';
        }
        catch (Exception $ex) {
            $result['error'] = $ex->getMessage();
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->asJson($result);
    }

    public function actionImages($id)
    {
        $model = $this->findModel($id);
        return $this->render('images', [
            'model' => $model,
            'images' => $this->service->getStoryImages($model->dropbox_story_filename),
        ]);
    }

}
