<?php


namespace console\controllers;


use backend\services\ImageService;
use common\models\StorySlideImage;
use http\Exception\RuntimeException;
use Yii;
use yii\console\Controller;

class ImageController extends Controller
{

    protected $service;

    public function __construct($id, $module, ImageService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function actionCheck()
    {
        $models = StorySlideImage::find()->all();
        $invalidImages = [];
        foreach ($models as $model) {
            $isValid = $this->service->checkImage($model->source_url);
            $model->status = $isValid ? StorySlideImage::STATUS_SUCCESS : StorySlideImage::STATUS_ERROR;
            $model->save(false, ['status']);
            if (!$isValid) {
                $invalidImages[] = $model;
            }
        }
        if (count($invalidImages) > 0) {
            $body = '<ul>';
            foreach ($invalidImages as $image) {
                $body .= '<li>' . $image->source_url . '</li>';
            }
            $body .= '</ul>';
            $sent = Yii::$app->mailer
                ->compose()
                ->setHtmlBody($body)
                ->setTo(Yii::$app->params['youtube.video.user.email'])
                ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
                ->setSubject('Wikids - Изображения больше недоступны')
                ->send();
            if (!$sent) {
                throw new RuntimeException('Ошибка при отправке email об недоступном изображении');
            }
        }
    }

}