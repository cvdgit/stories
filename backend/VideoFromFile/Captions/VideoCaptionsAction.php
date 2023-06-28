<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Captions;

use common\models\SlideVideo;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class VideoCaptionsAction extends Action
{
    public function run(int $id, Response $response)
    {
        $response->format = Response::FORMAT_RAW;
        $video = SlideVideo::findOne($id);
        if ($video === null) {
            throw new NotFoundHttpException('Видео не найдено');
        }
        if (count($video->captions) > 0) {
            return $video->captions[0]->content;
        }
    }
}
