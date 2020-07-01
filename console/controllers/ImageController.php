<?php


namespace console\controllers;


use backend\services\ImageService;
use common\helpers\EmailHelper;
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
            $response = EmailHelper::sendEmail(Yii::$app->params['youtube.video.user.email'], 'Wikids - Изображения больше недоступны', 'image-html', ['imageList' => $body]);
            if (!$response->isSuccess()) {
                throw new RuntimeException('Ошибка при отправке email об недоступном изображении');
            }
        }
    }

}