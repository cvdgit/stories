<?php

namespace console\controllers;

use backend\services\VideoService;
use common\models\SlideVideo;
use http\Exception\RuntimeException;
use Yii;
use yii\console\Controller;

class VideoController extends Controller
{

    protected $service;

    public function __construct($id, $module, VideoService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function actionCheck()
    {
        $models = SlideVideo::find()->all();
        $invalidVideos = [];
        foreach ($models as $model) {
            $isValid = $this->service->checkVideo($model->video_id);
            $model->status = $isValid ? SlideVideo::STATUS_SUCCESS : SlideVideo::STATUS_ERROR;
            $model->save(false, ['status']);
            if (!$isValid) {
                $invalidVideos[] = [
                    'title' => $model->title,
                ];
            }
        }
        if (count($invalidVideos) > 0) {
            $body = '<ul>';
            foreach ($invalidVideos as $item) {
                $body .= '<li>' . $item['title'] . '</li>';
            }
            $body .= '</ul>';
            $sent = Yii::$app->mailer
                ->compose()
                ->setHtmlBody($body)
                ->setTo(Yii::$app->params['youtube.video.user.email'])
                ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
                ->setSubject('Wikids - Найдены удаленные видео youtube')
                ->send();
            if (!$sent) {
                throw new RuntimeException('Ошибка при отправке email об удаленном видео');
            }
        }
    }

}