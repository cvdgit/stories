<?php

namespace frontend\controllers;

use Yii;
use common\models\Rate;
use common\models\User;
use common\service\CustomerPayment as PaymentService;

class RateController extends \yii\web\Controller
{
    
    private $userId;
    private $paymentService = null;

    public function __construct($id, $module, $config = [])
    {
        $this->paymentService = new PaymentService();
        $this->userId = Yii::$app->user->id;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $date_rate = null;
        $user = User::findOne($this->userId);
        if ($user !== null) {
            $date_rate = $this->paymentService->dateFinishPayment($user);
        }
        $rates = Rate::find()->all();
        $rates = $this->paymentService->addPaymentData($rates);
        return $this->render('index', [
            'user' => $user,
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