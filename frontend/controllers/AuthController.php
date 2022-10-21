<?php

namespace frontend\controllers;

use common\models\Story;
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
use yii\web\Controller;
use common\models\LoginForm;
use common\services\auth\AuthService;
use frontend\components\auth\AuthHandler;
use yii\web\NotFoundHttpException;
use yii\authclient\AuthAction;
use yii\web\Response;

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

    public function behaviors(): array
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

    public function actions(): array
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

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $returnUrl = null;
        $storyInfo = '';
        if (($backRoute = $this->service->getBackRoute(Yii::$app->user->returnUrl, Yii::$app->request->referrer)) !== null) {
            $returnUrl = $backRoute['url'];
            if (($story = Story::findOne([$backRoute['match']['field'] => $backRoute['match']['value'], 'status' => 1])) !== null) {
                $storyInfo = $story->title;
            }
        }

        $form = new LoginForm();
        $form->returnUrl = $returnUrl;

        if ($this->request->isPost && $form->load($this->request->post())) {
            try {
                $route = $this->service->auth($form);
                if (!empty($form->returnUrl)) {
                    $route = $returnUrl;
                }
                return $this->redirect($route);
            } catch (DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
            }
        }
        return $this->render('login', [
            'model' => $form,
            'storyInfo' => $storyInfo,
        ]);
    }

    public function actionLogout(): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        Yii::$app->user->logout();
        return $this->goHome();
    }

    private function goToTask($task): Response
    {
        if (($task !== null) && ($taskModel = StudyTask::findTask($task)) !== null) {
            return $this->redirect($taskModel->getStudyTaskUrl());
        }
        return $this->goHome();
    }

    public function actionToken(string $token, $task = null): Response
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
