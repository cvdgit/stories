<?php


namespace frontend\controllers;

use common\models\Rate;
use common\services\UserPaymentService;
use common\services\UserService;
use Exception;
use frontend\components\UserController;
use frontend\models\PaymentForm;
use Yii;
use common\models\SubscriptionForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\rest\Serializer;
use yii\web\Controller;
use yii\web\Response;
use common\models\User;

class PaymentController extends UserController
{

/*    protected $paymentService;
    protected $userService;

    public function __construct($id, $module, UserPaymentService $paymentService, UserService $userService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->paymentService = $paymentService;
        $this->userService = $userService;
    }*/

    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'notify'],
                'rules' => [
                    [
                        'actions' => ['notify'],
                        'allow' => true,
                    ],
                    [
                        //'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    // 'create' => ['post'],
                    'notify' => ['post'],
                ],
            ],
        ];
    }*/

/*    public function beforeAction($action)
    {
        if ($action->id === 'notify') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }*/

/*    public function actionIndex(): string
    {
        // @var User $user
        $user = Yii::$app->user->identity;
        return $this->render('index', [
            'payments' => $user->payments,
        ]);
    }*/

    /*
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $model = new SubscriptionForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                try {

                    $paymentID = $this->paymentService->createSubscription(Yii::$app->user->getId(), $model);

                    $paymentForm = new PaymentForm();
                    $paymentForm->terminalkey = Yii::$app->params['terminalkey'];

                    $user = User::findModel(Yii::$app->user->getId());
                    $paymentForm->email = $user->email;

                    $rate = $model->getRate();
                    $paymentForm->amount = $rate->cost;
                    $paymentForm->order = $paymentID;

                    $paymentForm->receipt = Json::encode($paymentForm->makeReceipt($rate));

                    $html = $this->renderPartial('_payment_form', ['model' => $paymentForm]);
                    $serializer = new Serializer();
                    return ['success' => true, 'html' => $serializer->serialize($html)];
                } catch (Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                    return ['success' => false, 'message' => $ex->getMessage()];
                }
            }
        }
        else {
            $ex = new Exception('Payment create bad request');
            Yii::$app->errorHandler->logException($ex);
        }
        return ['success' => false, 'message' => 'При создании платежа произошла ошибка'];
    }
    */

/*    public function actionNotify()
    {
        $post = Yii::$app->request->getRawBody();
        if (!empty($post)) {
            $post = Json::decode($post);
            $token = $this->paymentService->generateToken($post);
            if ($this->paymentService->checkToken($post, $token)) {
                try {
                    $this->paymentService->processPaymentNotify($post);
                    echo 'OK';
                }
                catch (Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                }
            }
            else {
                Yii::info($post, 'payment_fail');
            }
        }
    }*/

}
