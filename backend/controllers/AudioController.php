<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\StoryBreadcrumbsBuilder;
use backend\components\StorySideBarMenuItemsBuilder;
use backend\models\audio\AudioUploadForm;
use backend\models\audio\CreateAudioForm;
use backend\models\audio\UpdateAudioForm;
use common\models\AudioFile;
use common\models\Story;
use common\models\StoryAudioTrack;
use common\rbac\UserRoles;
use common\services\StoryAudioService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class AudioController extends Controller
{
    private $audioService;

    public function __construct($id, $module, StoryAudioService $audioService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->audioService = $audioService;
    }

    public function behaviors(): array
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
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $story_id): string
    {
        $model = Story::findOne($story_id);
        if ($model === null) {
            throw new NotFoundHttpException("История не найдена");
        }
        $dataProvider = new ActiveDataProvider([
            'query' => StoryAudioTrack::find()->andWhere(['story_id' => $story_id]),
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
            "sidebarMenuItems" => (new StorySideBarMenuItemsBuilder($model))->build(),
            "breadcrumbs" => (new StoryBreadcrumbsBuilder($model, 'Озвучка: ' . $model->title))->build(),
            "title" => 'Озвучка: ' . $model->title,
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

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionPlay(int $id)
    {
        $audioFile = AudioFile::findOne($id);
        if ($audioFile === null) {
            throw new NotFoundHttpException('Audio not found');
        }
        $filePath = $audioFile->getAudioFilePath();
        if (!file_exists($filePath)) {
            throw new HttpException(500, 'File not found');
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
