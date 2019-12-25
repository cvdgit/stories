<?php


namespace backend\controllers;


use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
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

    public function actionAuthorize()
    {
        return $this->redirect($this->_authorizeLink);
    }

    protected function getTokenFilePath()
    {
        return Yii::getAlias('@public/admin/upload/token');
    }

    public function actionToken($code)
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
        file_put_contents($this->getTokenFilePath(), $result);
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

    protected function getAuthToken()
    {
        if ($this->token === null) {
            $json = file_get_contents($this->getTokenFilePath());
            $json = Json::decode($json);
            $this->token = $json['access_token'];
        }
        return $this->token;
    }

    public function actionBoards(int $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = $this->getCurl()
            ->setHeader('Authorization', 'OAuth ' . $this->getAuthToken())
            ->setGetParams(['page' => $page])
            ->setGetParams(['page_size' => 100])
            ->get('https://api.collections.yandex.net/v1/boards/');
        return Json::decode($result);
    }

    public function actionCards(string $board_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //$this->checkTokenExpire();
        $result = $this->getCurl()
            ->setHeader('Authorization', 'OAuth ' . $this->getAuthToken())
            ->setGetParams(['board_id' => $board_id])
            ->get('https://api.collections.yandex.net/v1/cards/');
        return Json::decode($result);
    }

    protected function refreshToken()
    {
        $curl = $this->getCurl();
        $result = $curl
            ->setOption(CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => '',
            ]))
            ->post(self::TOKEN_URL);
        $result = Json::decode($result);
    }

}