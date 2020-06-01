<?php


namespace frontend\controllers;


use common\models\StorySlideImage;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use Yii\web\Response;

class ImageController extends Controller
{

    public function actionView(string $id)
    {
        $image = StorySlideImage::findOne(['hash' => $id]);
        if ($image === null) {
            throw new HttpException(404);
        }

        if (!$image->isSuccess()) {
            if ($image->linkImages === null) {
                throw new HttpException(404);
            }
            $image = $image->linkImages[0];
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('content-type', 'image/jpeg');
        try {
            $img_data = file_get_contents(Yii::getAlias('@public/admin/upload/') . $image->folder . '/' . $image->hash . '.jpeg');
        }
        catch (\Exception $ex) {
            throw new HttpException(404);
        }
        $response->data = $img_data;
        return $response;
    }

}