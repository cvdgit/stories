<?php

namespace backend\controllers;

use backend\models\UserCreateForm;
use backend\models\UserUpdateForm;
use common\services\UserService;
use DomainException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use common\services\UserPaymentService;
use common\models\User;
use common\models\PaymentSearch;
use common\rbac\UserRoles;
use yii\web\HttpException;

class UserController extends Controller
{

    protected $paymentService;
    protected $userService;

    public function __construct($id, $module, UserPaymentService $paymentService, UserService $userService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->paymentService = $paymentService;
        $this->userService = $userService;
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

    public function actionCreate()
    {
        $form = new UserCreateForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $user = $this->userService->create($form);
                return $this->redirect(['view', 'id' => $user->id]);
            } catch (DomainException $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $user = User::findModel($id);
        $form = new UserUpdateForm($user);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->userService->edit($user->id, $form);
                return $this->redirect(['view', 'id' => $user->id]);
            } catch (DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,
        ]);
    }

    public function actionDelete($id)
    {
        $model = User::findModel($id);
        if (count($model->stories) > 0) {
            throw new DomainException('Невозможно удалить пользователь т.к. у него есть истории');
        }
        $model->delete();
        return $this->redirect(['index']);
    }

    public function actionSubscriptions($id)
    {
        $userModel = User::findModel($id);
        $paymentSearch = New PaymentSearch();
        $dataProvider = $paymentSearch->search(Yii::$app->request->queryParams, $userModel->id);
        return $this->render('subscriptions', [
            'model' => $userModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionActivateSubscription($user_id)
    {
        $userModel = User::findModel($user_id);
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

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => User::findModel($id),
        ]);
    }

}
