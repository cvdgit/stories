<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class ImageController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        set_time_limit(0);
    }

    /**
     * @throws \Exception
     */
    public function actionGenerate(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $prompt = $payload['prompt'];

        $curl = (new Curl())
            ->setHeader('Accept', 'application/json')
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setOption(CURLOPT_CONNECTTIMEOUT, 0)
            ->setOption(
                CURLOPT_POSTFIELDS,
                Json::encode([
                    'prompt' => $prompt,
                ]),
            );

        $result = $curl->post(Yii::$app->params['gpt.api.image.host']);

        try {
            $result = Json::decode($result);
        } catch (\Exception $exception) {
        }

        if ($result === false) {
            return [
                'success' => false,
                'message' => "Error (Code: {$curl->errorCode}): {$curl->errorText}",
            ];
        }

        return [
            'success' => true,
            'data' => $result,
        ];
    }
}
