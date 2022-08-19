<?php

namespace frontend\controllers;

use common\models\StudyTask;
use common\models\UserToken;
use common\services\WelcomeUserService;
use DomainException;
use Exception;
use frontend\components\NoEmailException;
use frontend\components\UserAlreadyExistsException;
use Yii;
use yii\authclient\ClientInterface;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use common\models\LoginForm;
use common\services\auth\AuthService;
use frontend\components\auth\AuthHandler;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\authclient\AuthAction;

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
                'class' => AuthAction::class,
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess(ClientInterface $client): void
    {
        $handler = new AuthHandler($client);
        $authUserForm = $handler->handle();

        try {
            $this->service->socialAuth($client->getId(), $authUserForm);
        }
        catch (NoEmailException $noEmailException) {
            $this->service->noEmailRedirect($client->getId(), $authUserForm);
        }
        catch (UserAlreadyExistsException $userAlreadyExistsException) {
            Yii::$app->session->setFlash('error', 'Пользователь, с указанным в аккаунте ' . $client->getTitle() . ' email уже существует.');
        }
        catch(Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest && !$this->request->isAjax) {
            return $this->goHome();
        }

        $form = new LoginForm();
        if ($this->request->isPost && $form->load($this->request->post())) {
            try {
                $route = $this->service->auth($form);
                return [
                    'success' => true,
                    'returnUrl' => count($route) > 0 ? Url::to($route) : null,
                ];
            } catch (DomainException $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Произошла ошибка'];
            } finally {
                if (isset($e)) {
                    Yii::$app->errorHandler->logException($e);
                }
            }
        }
        //return ['success' => false];
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
