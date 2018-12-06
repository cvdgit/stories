<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\User;
use common\models\Payment;
use common\service\CustomerPayment as PaymentService;

class ProfileController extends \yii\web\Controller
{

    private $paymentService = null;
    private $userId;

    public function __construct($id, $module, $config = [])
    {
        $this->paymentService = new PaymentService();
        $this->userId = Yii::$app->user->id;
        parent::__construct($id, $module, $config);
    }

	public function behaviors()
	{
	    return [
	        'access' => [
	            'class' => AccessControl::className(),
	            'rules' => [
	                [
	                    'allow' => true,
	                    'roles' => ['author'],
	                ],
	            ],
	        ],
	    ];
	}

    public function actionIndex()
    {
        $user = User::findModel($this->userId);
        $date_rate = $this->paymentService->dateFinishPayment($user);
        $payment = $this->paymentService->getLastPaymentUser($user);

        return $this->render('index', [
            'model' => $user,
            'rate' => $payment->rate,
            'count_date_rate' => $date_rate,
        ]);
    }

    // TODO: добавить валидацию!
    public function actionChangePassword()
    {
        $request = Yii::$app->request->post();    
        $user = User::findModel($this->userId);
        if ($request["password"] === $request["password-repeat"]) {
            $user->setPassword($request["password"]);
            Yii::$app->session->setFlash('password-message', 'Новый пароль сохранен!');
            $user->save(false);
        } else {
            Yii::$app->session->setFlash('password-message', 'Ошибка! Пароль не изменен!');
        }
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

}
