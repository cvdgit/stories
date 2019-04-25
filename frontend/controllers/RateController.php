<?php

namespace frontend\controllers;

use common\services\UserService;
use Yii;
use yii\web\Controller;
use common\services\UserPaymentService;
use frontend\models\SubscriptionForm;

class RateController extends Controller
{

    protected $paymentService;
    protected $userService;

    public function __construct($id, $module, UserPaymentService $paymentService, UserService $userService, $config = [])
    {
        $this->paymentService = $paymentService;
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $model = new SubscriptionForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->paymentService->activateSubscription(Yii::$app->user->id, $model->subscription_id);
            Yii::$app->session->setFlash('success', 'Подписка успешно активирована');
            return $this->redirect(['/profile']);
        }

        $hasSubscription = !Yii::$app->user->isGuest && $this->userService->hasSubscription(Yii::$app->user->getId());

        return $this->render('index', [
            'rates' => $this->paymentService->getRates(),
            'model' => $model,
            'hasSubscription' => $hasSubscription,
        ]);
    }

}
