<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class StreamController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionChat(Request $request, Response $response)
    {

        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        \Yii::$app->session->close();

        @ob_end_clean();
        ini_set('output_buffering', '0');
        //set_time_limit(0);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache, must-revalidate");
        header("X-Accel-Buffering: no");
        header("Connection: keep-alive");

        $fields = [
            "content" => $request->post("content"),
            "questions" => $request->post("questions"),
            "answers" => $request->post("answers"),
        ];

        $options = [
            CURLOPT_URL => \Yii::$app->params["gpt.api.host"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => Json::encode($fields),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_WRITEFUNCTION => function($ch, $chunk) {
                echo $chunk;
                //sleep(1);
                flush();
                return strlen($chunk);
            },
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        curl_exec($ch);
        curl_close($ch);

        //$response->statusCode = 404;
        //$response->data = 'no';
    }
}
