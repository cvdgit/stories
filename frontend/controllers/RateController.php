<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\services\UserPaymentService;
use common\models\Rate;
use common\models\SubscriptionModel;

class RateController extends Controller
{

    protected $paymentService;

    public function __construct($id, $module, UserPaymentService $paymentService, $config = [])
    {
        $this->paymentService = $paymentService;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $model = new SubscriptionModel();
        $model->scenario = SubscriptionModel::SCENARIO_FRONTEND;

        $hasSubscription = false;
        if (!Yii::$app->user->isGuest) {
            $hasSubscription = Yii::$app->user->identity->hasSubscription();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $rate = Rate::findOne($model->subscription_id);
            $model->calculateSubscriptionDates($rate->mounth_count);
            $this->paymentService->activateSubscription(Yii::$app->user->id, $model);
            Yii::$app->session->setFlash('success', 'Подписка успешно активирована');
            return $this->redirect(['/profile']);
        }

        return $this->render('index', [
            'rates' => Rate::find()->all(),
            'model' => $model,
            'hasSubscription' => $hasSubscription,
        ]);
    }

}
