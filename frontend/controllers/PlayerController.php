<?php


namespace frontend\controllers;


use common\models\Story;
use common\models\StoryAudioTrack;
use common\services\StoryAudioService;
use common\services\StoryService;
use Exception;
use frontend\models\SlideAudio;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class PlayerController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['moderator'],
                    ],
                ],
            ],
        ];
    }

    protected $audioService;

    public function __construct($id, $module, StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
        parent::__construct($id, $module, $config);
    }

    public function actionSetSlideAudio()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SlideAudio();
        $result = ['success' => false, 'message' => ''];
        if ($model->load(Yii::$app->request->post())) {
            $model->slide_audio_files = UploadedFile::getInstances($model, 'slide_audio_files');
            try {
                $result['success'] = $model->upload();
            }
            catch (Exception $ex) {
                $result['message'] = $ex->getMessage();
            }
            if ($model->hasErrors()) {
                die(print_r($model->errors));
            }
            if ($result['success']) {
                $this->audioService->setSlideAudio($model);
            }
        }
        return $result;
    }

    public function actionCreateAudioTrack(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($story_id);
        $trackID = $this->audioService->createTrack('Пользовательская', $model->id, Yii::$app->user->id, StoryAudioTrack::TYPE_USER, 0);
        return ['success' => true, 'track' => StoryAudioTrack::findModel($trackID)];
    }

    public function actionGetTrack(int $track_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => true, 'track' => StoryAudioTrack::findModel($track_id)];
    }

}