<?php
namespace common\service;

use Application as AppService;
use common\models\Payment;
use common\models\Rate;

class CustomerPayment {
 
    /**
     * TODO: убрать в общие настройки
     */
    private $url = 'https://merchant.roboxchange.com/Index.aspx';
    private $paymentId = 0;
    private $login = "demo"; // TODO: тестовое поле
    private $inCurl = "";
    private $culture = "ru";
    private $password1 = "password_1";  // TODO: тестовое поле
    private $password2 = "password_2";  // TODO: тестовое поле
    private $description = "Истории";

    public function fail() 
    {
        echo "Вы отказались от оплаты";
        $request = Yii::$app->request->post();
        var_dump( $request); die();
    }

    public function success() 
    {
        echo 'success';
        $request = Yii::$app->request->post();
        var_dump( $request); die();

        //1. Проверка номера заказа
        //2. Проверка корректности подписи
        $this->createPayment(2);
    }

    /**
     * Получить crc - считается перед отправкой платежа
     * @param $rateCost - сумма подписки
     * @param $paymentId - номер заказа
     * @param $shpItem - тип подписки
     * @return string
     */
    private function getCrc($rateCost, $shpItem)
    {
        $crc = $this->login.":".$rateCost.":".$this->paymentId.":".$this->password1.":Shp_item=".$shpItem;
        return strtoupper(md5($crc));
    }

    /**
     * Получить данные для оплаты
     * @param $rateCost - сумма подписки
     * @param $shpItem - тип подписки
     * @return string
     */
    public function getDataPayment($shpItem, $rateCost)
    {
        return [
            'url' => $this->url,
            'MrchLogin' => $this->login,
            'InvId' => $this->paymentId,
            'Desc' => $this->description,
            'SignatureValue' => $this->getCrc($rateCost, $shpItem),
            'IncCurrLabel' => $this->inCurl,
            'Culture' => $this->culture,
        ];
    }

    /**
     * Создание оплаты
     * TODO: что происходит при продлении подписки, Rate - убарть!
     */
    private function createPayment($rate_id)
    {
        $rate = Rate::findOne($rate_id);
        $finish = AppService::addMounth(date('Y-m-d H:i:s'), $rate->mounth_count);
        
        $post = new Payment;
        $post->payment = date('Y-m-d H:i:s');
        $post->finish = $finish;
        $post->user_id = $this->user_id;
        $post->rate_id = $rate_id;
        $post->save();
    }

}