<?php


namespace console\controllers;


use common\models\Payment;
use common\services\UserPaymentService;
use common\services\UserService;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;

class SubscriptionController extends Controller
{

    protected $paymentService;
    protected $userService;

    public function __construct($id, $module, UserPaymentService $paymentService, UserService $userService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->paymentService = $paymentService;
        $this->userService = $userService;
    }

    public function actionCancel()
    {
        $query = (new Query())
            ->from('{{%payment}}')
            ->where(['<=','finish', new Expression('NOW()')])
            ->andWhere('state = :valid', [':valid' => Payment::STATUS_VALID]);
        foreach ($query->each() as $payment) {
            echo $payment['id'] . ' - ' . $payment['payment'] . ' - ' . $payment['finish'] . PHP_EOL;
            // $this->paymentService->cancelSubscription($payment['id']);
        }
    }

}