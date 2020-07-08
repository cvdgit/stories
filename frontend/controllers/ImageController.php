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

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            header('HTTP/1.1 304 Not Modified');
            die();
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

        $imagePath = Yii::getAlias('@public/admin/upload/') . $image->folder . '/' . $image->hash . '.jpeg';
        $response->headers->add('Cache-control', 'max-age=' . (60*60*24*365));
        $response->headers->add('Expires', gmdate(DATE_RFC1123,time()+60*60*24*365));
        $response->headers->add('Last-Modified', gmdate(DATE_RFC1123, filemtime($imagePath)));
        $response->headers->add('ETag', sprintf('%08x-%08x', crc32($imagePath), filemtime($imagePath)));

        try {
            $img_data = file_get_contents($imagePath);
        }
        catch (\Exception $ex) {
            throw new HttpException(404);
        }
        $response->data = $img_data;
        return $response;
    }

}