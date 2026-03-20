<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\StorySlideImage;
use Exception;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\imagine\Image;
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
        catch (Exception $ex) {
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionGet(string $path, string $type = 'thumb'): Response
    {
        ini_set('memory_limit', '1024M');
        $imagePath = Yii::getAlias('@public/upload') . $path;
        if (realpath($imagePath) !== $imagePath) {
            throw new NotFoundHttpException('Изображение не найдено');
        }

        if (!file_exists($imagePath)) {
            throw new NotFoundHttpException('Изображение не найдено');
        }

        [$imageWidth, $imageHeight, $imageType] = getimagesize($imagePath);
        $imageType = image_type_to_mime_type($imageType);

        if ($type === "original") {
            return $this->sendImageResponse($imageType, $imagePath);
        }

        if ($type === "cover") {
            $width = 340;
            $thumbFileName = $width . "-" . basename($imagePath);
            $thumbImagePath = Yii::getAlias("@public/upload/thumbs/$thumbFileName");
            if (!file_exists($thumbImagePath)) {
                Image::thumbnail($imagePath, $width, null, ManipulatorInterface::THUMBNAIL_INSET)
                    ->save($thumbImagePath, ['quality' => 80]);
            }
        } else {
            $width = 640;
            $height = 480;
            $thumbFileName = $width . 'x' . $height . '-' . basename($imagePath);
            $thumbImagePath = Yii::getAlias('@public/upload/thumbs/') . $thumbFileName;
            if (!file_exists($thumbImagePath)) {
                Image::thumbnail($imagePath, $width, $height, ManipulatorInterface::THUMBNAIL_INSET)
                    ->save($thumbImagePath, ['quality' => 80]);
            }
        }

        return $this->sendImageResponse($imageType, $thumbImagePath);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function sendImageResponse(string $imageType, string $imagePath): Response
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;

        $headers = $response->headers;
        $headers->removeAll();
        $headers->add('content-type', $imageType);
        $headers->add('Cache-control', 'max-age=' . (60*60*24*365));
        $headers->add('Expires', gmdate(DATE_RFC1123,time()+60*60*24*365));
        $headers->add('Last-Modified', gmdate(DATE_RFC1123, filemtime($imagePath)));
        $headers->add('ETag', sprintf('%08x-%08x', crc32($imagePath), filemtime($imagePath)));

        try {
            $response->data = file_get_contents($imagePath);
        }
        catch (Exception $ex) {
            throw new NotFoundHttpException('Изображение не найдено');
        }

        return $response;
    }
}
