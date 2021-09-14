<?php

namespace frontend\controllers;

use common\models\StudyTask;
use common\models\UserToken;
use common\services\WelcomeUserService;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\models\LoginForm;
use common\services\auth\AuthService;
use frontend\components\AuthHandler;
use yii\web\NotFoundHttpException;

class AuthController extends Controller
{

    protected $service;
    protected $welcomeUserService;

    public function __construct($id, $module, AuthService $service, WelcomeUserService $welcomeUserService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->welcomeUserService = $welcomeUserService;
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
        (new AuthHandler($client, $this->welcomeUserService))->handle();
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
        //if (Yii::$app->request->isAjax) {
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
        //}
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

    private function goToTask($task)
    {
        if (($task !== null) && ($taskModel = StudyTask::findTask($task)) !== null) {
            return $this->redirect($taskModel->getStudyTaskUrl());
        }
        return $this->goHome();
    }

    public function actionToken(string $token, $task = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goToTask($task);
        }
        $tokenModel = UserToken::findByToken($token);
        if ($tokenModel === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        if ($tokenModel->isExpired()) {
            throw new NotFoundHttpException('Истекший токен');
        }
        if (($user = $tokenModel->user) === null) {
            throw new NotFoundHttpException('Ошибка пользователя');
        }

        Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
        $tokenModel->resetToken();

        try {
            $this->welcomeUserService->addJob($user->id);
        }
        catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
        }

        return $this->goToTask($task);
    }
}
