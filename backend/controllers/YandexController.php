<?php


namespace backend\controllers;


use http\Exception\RuntimeException;
use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class YandexController extends Controller
{

    const AUTHORIZE_URL = 'https://oauth.yandex.ru/authorize';
    const TOKEN_URL = 'https://oauth.yandex.ru/token';

    private $clientID;
    private $clientSecret;
    private $_authorizeLink;

    private $_curl;

    public function __construct($id, $module, $config = [])
    {
        $this->clientID = Yii::$app->params['yandex.client_id'];
        $this->clientSecret = Yii::$app->params['yandex.client_secret'];
        $this->_authorizeLink = self::AUTHORIZE_URL . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientID,
            ]);
        parent::__construct($id, $module, $config);
    }

    protected function getCurl()
    {
        if ($this->_curl === null) {
            $this->_curl = new Curl();
        }
        $this->_curl->reset();
        return $this->_curl;
    }

    public function actionAuthorize(string $account)
    {
        return $this->redirect($this->_authorizeLink . '&state=' . $account);
    }

    protected function checkAccount($account)
    {
        $accounts = Yii::$app->params['yandex.accounts'];
        return isset($accounts[$account]);
    }

    protected function getTokenFilePath(string $account)
    {
        if (!$this->checkAccount($account)) {
            throw new HttpException(400, 'Invalid account');
        }
        return Yii::getAlias('@public/admin/upload/' . $account);
    }

    public function actionToken($code, $state = '')
    {
        $curl = new Curl();
        $result = $curl
            ->setOption(CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $this->clientID,
                'client_secret' => $this->clientSecret,
            ]))
            ->post(self::TOKEN_URL);
        file_put_contents($this->getTokenFilePath($state), $result);
    }

    protected function checkTokenExpire()
    {
        $json = file_get_contents($this->getTokenFilePath());
        $json = Json::decode($json);
        var_dump(Yii::$app->formatter->asDatetime(time()));
        die();
        //return time() + $expire < time();
    }

    private $token;

    protected function getAuthToken(string $account)
    {
        $tokenPath = $this->getTokenFilePath($account);
        if (!file_exists($tokenPath)) {
            throw new HttpException(400,'Token not found');
        }
        $json = file_get_contents($tokenPath);
        $json = Json::decode($json);
        $this->token = $json['access_token'];
        return $this->token;
    }

    protected function query(string $httpMethod, string $apiMethod, array $params = []) {
        $this->token = 'AgAAAAA4xBBoAAYMF1BRwQwQfU59ingSfBfwDb8';
        $params['headers'] = ['Authorization' => 'OAuth ' . $this->token];
        if ($httpMethod == 'GET') {
            $params['headers']['Accept'] = 'application/json';
        } else if ($httpMethod == 'POST' || $httpMethod == 'PATCH') {
            $params['headers']['Content-Type'] = 'application/json; charset=utf-8';
        }
        $httpClient = new \GuzzleHttp\Client;
        $res = $httpClient->request($httpMethod, 'https://api.collections.yandex.net' . $apiMethod, $params);
        if ($res->getStatusCode() == 204) {
            return true;
        }
        return \GuzzleHttp\json_decode($res->getBody()->getContents());
    }

    public function actionBoards(string $account, int $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /*
        $result = $this->getCurl()
            ->setHeader('Accept', 'application/json')
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setHeader('Authorization', 'OAuth ' . $this->getAuthToken($account))
            ->setGetParams(['page' => $page])
            ->setGetParams(['page_size' => 10])
            ->get('https://api.collections.yandex.net/v1/boards/');
        return Json::decode($result);
        */
        $params['query'] = ['page' => $page, 'page_size' => 10];
        return $this->query('GET', '/v1/boards/', $params);
    }

    public function actionCards(string $board_id, string $account)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        //$this->checkTokenExpire();
        $result = $this->getCurl()
            ->setHeader('Authorization', 'OAuth ' . $this->getAuthToken($account))
            ->setGetParams(['board_id' => $board_id])
            ->setGetParams(['page' => 1])
            ->setGetParams(['page_size' => 100])
            ->get('https://api.collections.yandex.net/v1/cards/');
        return Json::decode($result);
    }

    protected function refreshToken()
    {
        $curl = $this->getCurl();
        $result = $curl
            ->setOption(CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => '1:vv2pooU-Eiqzfvo_:7IJS3rvGHH4I_a5wurn-6YQ2PaxSFeN5Mk0ucrZMIvD-yoCZ6uiu:M5pgBLGa04JA_gx6vD1YCg',
            ]))
            ->post(self::TOKEN_URL);
        return Json::decode($result);
    }

    public function actionRefreshToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->refreshToken();
    }

}