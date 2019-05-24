<?php

namespace backend\controllers;

use backend\models\ChangePasswordForm;
use backend\models\UserCreateForm;
use backend\models\UserUpdateForm;
use common\models\SubscriptionForm;
use common\services\UserService;
use DomainException;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use common\services\UserPaymentService;
use common\models\User;
use common\models\PaymentSearch;
use common\rbac\UserRoles;
use yii\web\Response;

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

        $paymentSearch = new PaymentSearch();
        $dataProvider = $paymentSearch->search(Yii::$app->request->queryParams, $user->id);

        return $this->render('update', [
            'model' => $form,
            'dataProvider' => $dataProvider,
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

    public function actionCreateSubscription($user_id)
    {
        $user = User::findModel($user_id);
        $subscriptionForm = new SubscriptionForm();
        if (Yii::$app->request->isAjax && $subscriptionForm->load(Yii::$app->request->post()) && $subscriptionForm->validate()) {
            try {
                $paymentID = $this->paymentService->createSubscription($user->id, $subscriptionForm);
                $this->paymentService->activateSubscription($paymentID);
                return Json::encode(['success' => true]);
            }
            catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                return Json::encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        return $this->renderAjax('_activate_subscription', [
            'model' => $subscriptionForm,
        ]);
    }

    public function actionActivateSubscription($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->paymentService->activateSubscription($id);
        return ['success' => true];
    }

    public function actionCancelSubscription($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->paymentService->cancelSubscription($id);
        return ['success' => true];
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => User::findModel($id),
        ]);
    }

    public function actionChangePassword($id)
    {
        $user = User::findModel($id);
        $form = new ChangePasswordForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $user->setPassword($form->password);
            $user->save();
            Yii::$app->session->setFlash('success', 'Пароль успешно изменен');
            return $this->refresh();
        }
        return $this->render('password', [
            'model' => $form,
        ]);
    }

}
