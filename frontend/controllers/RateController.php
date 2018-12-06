<?php

namespace frontend\controllers;

use Yii;
use common\models\Rate;
use common\models\User;
use common\service\CustomerPayment as PaymentService;

class RateController extends \yii\web\Controller
{
    
    private $userId = 2;
    private $paymentService = null;

    public function __construct($id, $module, $config = [])
    {
        $this->paymentService = new PaymentService();
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $user = User::findModel($this->userId);
        $date_rate = $this->paymentService->dateFinishPayment($user);
        $rates = Rate::find()->all();
        $rates = $this->paymentService->addPaymentData($rates);
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

}