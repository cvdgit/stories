<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

trait StreamTrait
{
    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        @ob_end_clean();
        ini_set('output_buffering', '0');
        set_time_limit(0);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache, must-revalidate");
        header("X-Accel-Buffering: no");
        header("Connection: keep-alive");

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        Yii::$app->session->close();

        return parent::beforeAction($action);
    }
}
