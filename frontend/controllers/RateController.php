<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\Rate;
use common\models\User;
use common\service\CustomerPayment as PaymentService;
use common\service\Application as AppService;

class RateController extends \yii\web\Controller
{
    
    private $user_id = 1;
    private $paymentService = null;

    public function __construct($id, $module, $config = [])
    {
        $this->paymentService = new PaymentService();
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $finish = $this->userRateFinish();
        $date_rate = null;
        if ($finish) {
            $date_rate = AppService::getDayCount($finish);
        }
        $rates = Rate::find()->all();
        $rates = $this->addPaymentData($rates);
        return $this->render('index', [
            'rates' => $rates,
            'count_date_rate' => $date_rate,
        ]);
    }

    /**
     * Вызывается при неуспешной оплате
     */
    public function actionFail() 
    {
        $this->paymentService->fail();
    }

    /**
     * Вызывается при успешной оплате
     */
    public function actionSuccess() 
    {
        $this->paymentService->success();
    }

    /**
     * Добавление к подпискам данные оплаты
     */
    private function addPaymentData($rates)
    {
        $ratesWithPayment = [];
        foreach($rates as $rate) {
            $rate->setDataPayment(
                $this->paymentService->getDataPayment($rate->id, $rate->cost)
            );
        }
        return $rates;
    }

    /**
     * Получить дату окончания подписки пользователя
     * TODO: метод модели User
     */
    private function userRateFinish()
    {
        $user = User::findOne($this->user_id);
        return $user->getPayments()->max('finish');
    }

}
