<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\SlideVideo;
use frontend\components\VideoStream;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class VideoController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionStream(int $id, Response $response): void
    {
        $video = SlideVideo::findOne($id);
        if ($video === null) {
            throw new NotFoundHttpException('Видео не найдено');
        }

        $response->format = Response::FORMAT_RAW;

        $videoStream = new VideoStream($video->getFilePath());
        $videoStream->start();
    }
}
