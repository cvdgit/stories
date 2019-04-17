<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\User;
use common\models\Payment;
use common\service\CustomerPayment as PaymentService;
use frontend\models\ChangePasswordForm;

class ProfileController extends Controller
{

    private $paymentService;
    private $userId;

    public function __construct($id, $module, $config = [])
    {
        $this->paymentService = new PaymentService();
        $this->userId = Yii::$app->user->id;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex(): string
    {
        $user = User::findModel(Yii::$app->user->id);
        /*
        $user = User::findModel($this->userId);
        $date_rate = $this->paymentService->dateFinishPayment($user);
        $payment = $this->paymentService->getLastPaymentUser($user);
        $modelPassword = new ChangePasswordForm();

        return $this->render('index', [
            'modelPassword' => $modelPassword,
            'model' => $user,
            'rate' => ($payment !== null ? $payment->rate : null),
            'count_date_rate' => $date_rate,
        ]);
        */

        return $this->render('index', [
            'model' => $user,
            'activePayment' => $user->getActivePayment()->one(),
        ]);
    }

    public function actionChangePassword()
    {
        try {
            $model = new ChangePasswordForm();
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->changePassword()) {
            Yii::$app->session->setFlash('password-message', 'Новый пароль сохранен!');
        }
        else {
            Yii::$app->session->setFlash('password-message', 'Ошибка! Пароль не изменен!');
        }
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

}