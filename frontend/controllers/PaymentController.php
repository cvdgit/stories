<?php


namespace frontend\controllers;

use common\models\Rate;
use common\services\UserPaymentService;
use common\services\UserService;
use Exception;
use frontend\models\PaymentForm;
use Yii;
use frontend\models\SubscriptionForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\rest\Serializer;
use yii\web\Controller;
use yii\web\Response;
use common\models\User;

class PaymentController extends Controller
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
                'only' => ['create', 'notify'],
                'rules' => [
                    [
                        'actions' => ['notify'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'notify' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id === 'notify') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $model = new SubscriptionForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                try {

                    $paymentID = $this->paymentService->activateSubscription(Yii::$app->user->getId(), $model);

                    $paymentForm = new PaymentForm();
                    $paymentForm->terminalkey = Yii::$app->params['terminalkey'];

                    $user = User::findModel(Yii::$app->user->getId());
                    $paymentForm->email = $user->email;

                    /** @var $rate Rate */
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

    public function actionNotify()
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
    }

}