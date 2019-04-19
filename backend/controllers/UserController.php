<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use common\services\UserPaymentService;
use common\models\User;
use common\models\PaymentSearch;
use common\rbac\UserRoles;
use backend\models\SubscriptionForm;

class UserController extends Controller
{

    public $paymentService;

    public function __construct($id, $module, UserPaymentService $paymentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->paymentService = $paymentService;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_USERS],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSubscriptions($id)
    {
        $userModel = $this->findModel($id);
        $paymentSearch = New PaymentSearch();
        $dataProvider = $paymentSearch->search(Yii::$app->request->queryParams, $userModel->id);
        return $this->render('subscriptions', [
            'model' => $userModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionActivateSubscription($user_id)
    {
        $userModel = $this->findModel($user_id);
        $subscriptionModel = new SubscriptionForm();
        if (Yii::$app->request->isAjax) {
            if ($subscriptionModel->load(Yii::$app->request->post()) && $subscriptionModel->validate()) {
                try {
                    $this->paymentService->activateSubscription($userModel->id, $subscriptionModel);
                    return Json::encode(['success' => true]);
                }
                catch (\Exception $ex) {
                    return Json::encode(['success' => false, 'error' => $ex->getMessage()]);
                }
            }
        }
        return $this->renderAjax('_activate_subscription', [
            'subscription' => $subscriptionModel,
        ]);
    }

    public function actionCancelSubscription($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->paymentService->cancelSubscription($id);
        return ['success' => true];
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Пользователь не найден.');
    }

}
