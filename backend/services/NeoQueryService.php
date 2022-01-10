<?php

namespace backend\services;

use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\web\HttpException;

class NeoQueryService
{

    private $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    private function decodeQueryResult($result)
    {
        if (empty($result)) {
            throw new HttpException(500, 'No data');
        }
        try {
            $result = Json::decode($result);
        }
        catch (\Exception $ex) {
            throw new HttpException(500, 'Incorrect JSON');
        }

        if (isset($result['type']) && mb_strtolower($result['type']) === 'error') {
            Yii::error($result, 'neo.load.test');
            throw new HttpException(500, 'Request error');
        }

        if (!isset($result['total'])) {
            Yii::error($result, 'neo.load.test');
            throw new HttpException(500, 'Request error');
        }

        return $result;
    }

    public function query(int $questionId, string $questionParams = null, string $wrongAnswersParams = null)
    {

        $params = ['id' => $questionId];
        if ($questionParams !== null) {
            $params['params'] = $questionParams;
        }
        $postParams = [
            'wrong_params' => $wrongAnswersParams ?: '',
        ];

        $result = $this->curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_TIMEOUT => 300,
                CURLOPT_CONNECTTIMEOUT => 300,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams($params)
            ->setPostParams($postParams)
            ->get(Yii::$app->params['neo.url'] . '/api/question/get');

        return $this->decodeQueryResult($result);
    }
}
