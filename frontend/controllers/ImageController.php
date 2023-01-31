<?php

namespace frontend\controllers;

use common\models\StorySlideImage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii\web\Response;

class ImageController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionView(string $id)
    {
        $image = $this->findModelByHash($id);

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            header('HTTP/1.1 304 Not Modified');
            die();
        }

        if (!$image->isSuccess()) {
            if ($image->linkImages === null) {
                throw new NotFoundHttpException('Изображение не найдено');
            }
            $image = $image->linkImages[0];
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;

        $imagePath = $image->getImagePath();

        $headers = $response->headers;
        $headers->removeAll();
        $headers->add('content-type', empty($image->mime_type) ? 'image/jpeg' : $image->mime_type);
        $headers->add('Cache-control', 'max-age=' . (60*60*24*365));
        $headers->add('Expires', gmdate(DATE_RFC1123,time()+60*60*24*365));
        $headers->add('Last-Modified', gmdate(DATE_RFC1123, filemtime($imagePath)));
        $headers->add('ETag', sprintf('%08x-%08x', crc32($imagePath), filemtime($imagePath)));

        try {
            $img_data = file_get_contents($imagePath);
        }
        catch (\Exception $ex) {
            throw new NotFoundHttpException('Изображение не найдено');
        }
        $response->data = $img_data;
        return $response;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findModelByHash(string $hash): StorySlideImage
    {
        if (($model = StorySlideImage::findOne(['hash' => $hash])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Изображение не найдено');
    }
}
