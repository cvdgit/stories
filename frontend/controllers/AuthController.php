<?php


namespace frontend\controllers;

use common\models\Auth;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use common\models\LoginForm;
use common\services\auth\AuthService;
use frontend\components\AuthHandler;

class AuthController extends Controller
{

    protected $service;

    public function __construct($id, $module, AuthService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
            return $this->goHome();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $form = new LoginForm();
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                try {
                    $user = $this->service->auth($form);
                    Yii::$app->user->login($user, $form->rememberMe ? Yii::$app->params['user.rememberMeDuration'] : 0);
                    return ['success' => true, 'message' => ''];
                } catch (\DomainException $e) {
                    Yii::$app->errorHandler->logException($e);
                    return ['success' => false, 'message' => [$e->getMessage()]];
                }
            }
            else {
                return ['success' => false, 'message' => $form->errors];
            }
        }
        return ['success' => false, 'message' => ['']];
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionTest()
    {
        Yii::$app->session->set(Auth::AUTH_SESSION_KEY, [
            'source' => 'vkontakte',
            'source_id' => '123',
            'username' => 'test_vk',
        ]);
        Yii::$app->response->redirect(Url::to('/signup/email'));
    }

}