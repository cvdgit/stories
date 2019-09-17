<?php


namespace frontend\controllers;


use common\services\StoryService;
use Exception;
use frontend\models\SlideAudio;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class PlayerController extends Controller
{

    protected $storyService;

    public function __construct($id, $module, StoryService $storyService, $config = [])
    {
        $this->storyService = $storyService;
        parent::__construct($id, $module, $config);
    }

    public function actionSetSlideAudio()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SlideAudio();
        $result = ['success' => false, 'message' => ''];
        if ($model->load(Yii::$app->request->post())) {
            $model->slide_audio_file = UploadedFile::getInstance($model, 'slide_audio_file');
            try {
                $result['success'] = $model->upload();
            }
            catch (Exception $ex) {
                $result['message'] = $ex->getMessage();
            }
            if ($result['success']) {
                $this->storyService->setSlideAudio($model);
            }
        }
        return $result;
    }

}