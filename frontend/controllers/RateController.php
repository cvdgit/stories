<?php

namespace frontend\controllers;

use common\services\UserService;
use Exception;
use Yii;
use yii\web\Controller;
use common\services\UserPaymentService;
use common\models\SubscriptionForm;

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
            try {
                $this->paymentService->createSubscription(Yii::$app->user->id, $model);
                Yii::$app->session->setFlash('success', 'Подписка успешно активирована');
            }
            catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'При активации подписки произошла ошибка');
            }
            return $this->redirect(['/profile']);
        }
        $hasSubscription = !Yii::$app->user->isGuest && $this->userService->hasSubscription(Yii::$app->user->getId());
        $hasFreeSubscription = !Yii::$app->user->isGuest && $this->userService->hasValidFreeSubscription(Yii::$app->user->getId());
        return $this->render('index', [
            'hasSubscription' => $hasSubscription,
            'hasFreeSubscription' => $hasFreeSubscription,
        ]);
    }

}
