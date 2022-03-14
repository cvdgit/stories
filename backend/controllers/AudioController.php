<?php

namespace backend\controllers;

use backend\models\audio\AudioUploadForm;
use backend\models\audio\CreateAudioForm;
use backend\models\audio\UpdateAudioForm;
use common\models\AudioFile;
use common\models\Story;
use common\models\StoryAudioTrack;
use common\rbac\UserRoles;
use common\services\StoryAudioService;
use http\Exception\RuntimeException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class AudioController extends Controller
{

    protected $audioService;

    public function __construct($id, $module, StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
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

    public function actionIndex(int $story_id)
    {
        $model = Story::findModel($story_id);
        $query = StoryAudioTrack::find();
        $query->andFilterWhere(['story_id' => $story_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(int $story_id)
    {
        $model = Story::findModel($story_id);
        $form = new CreateAudioForm($model->id, Yii::$app->user->id);
        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->audioUploadForm->validate()) {
            $trackID = $form->createTrack();
            $form->audioUploadForm->audioFiles = UploadedFile::getInstances($form->audioUploadForm, 'audioFiles');
            $form->uploadTrackFiles($trackID);
            return $this->redirect(['index', 'story_id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $form = new UpdateAudioForm($id);
        $model = Story::findModel($form->story_id);
        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->audioUploadForm->validate()) {
            $form->updateTrack();
            $form->audioUploadForm->audioFiles = UploadedFile::getInstances($form->audioUploadForm, 'audioFiles');
            $form->uploadTrackFiles();
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $form,
            'storyModel' => $model,
        ]);
    }

    public function actionDeleteFile(int $story_id, int $track_id, string $file)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new AudioUploadForm($story_id);
        $form->audioTrackID = $track_id;
        return ['success' => $form->deleteAudioFile($file)];
    }

    public function actionDelete(int $id)
    {
        $model = StoryAudioTrack::findModel($id);
        $model->delete();
        return $this->redirect(['index', 'story_id' => $model->story_id]);
    }

    public function actionPublish(int $story_id)
    {
        $model = Story::findModel($story_id);
        $track = $model->getOriginalTrack();
        if (!$track) {
            Yii::$app->session->setFlash('error', 'Не удалось определить оригинальную дорожку');
        }
        else {
            try {
                $this->audioService->publishTrack($track);
                Yii::$app->session->setFlash('success', 'Озвучка опубликована');
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Ошибка публикации: ' . $e->getMessage());
            }
        }
        return $this->redirect(['story/update', 'id' => $model->id]);
    }

    public function actionUnpublish(int $story_id)
    {
        $model = Story::findModel($story_id);
        $track = $model->getOriginalTrack();
        if (!$track) {
            Yii::$app->session->setFlash('error', 'Не удалось определить оригинальную дорожку');
        }
        else {
            $this->audioService->unPublishTrack($track);
            Yii::$app->session->setFlash('success', 'Озвучка для истории снята с публикации');
        }
        return $this->redirect(['story/update', 'id' => $model->id]);
    }

    public function actionPlay(int $id)
    {
        $audioFile = AudioFile::findOne($id);
        if ($audioFile === null) {
            throw new NotFoundHttpException('Audio not found');
        }
        $filePath = $audioFile->storyTestQuestions[0]->getAudioFilesPath() . '/' . $audioFile->audio_file;
        if (!file_exists($filePath)) {
            throw new RuntimeException('File not found');
        }
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $headers = $response->headers;
        $headers->removeAll();
        $headers->add('content-type', 'audio/wav');
        $response->data = file_get_contents($filePath);
        return $response;
    }
}