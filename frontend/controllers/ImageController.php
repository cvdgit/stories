<?php


namespace frontend\controllers;


use common\models\StorySlideImage;
use Yii;
use yii\web\Controller;
use Yii\web\Response;

class ImageController extends Controller
{

    public function actionView(string $id)
    {
        $image = StorySlideImage::findOne(['hash' => $id]);
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('content-type', 'image/jpg');
        $img_data = file_get_contents(Yii::getAlias('@public/admin/upload/') . $image->folder . '/' . $image->hash . '.jpeg');
        $response->data = $img_data;
        return $response;
    }

}