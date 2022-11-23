<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Auth;
use common\models\User;
use common\services\WelcomeUserService;
use Exception;
use frontend\models\EmailForm;
use Yii;
use yii\web\Controller;
use yii\web\Request;
use frontend\models\SignupForm;
use common\services\auth\SignupService;

class SignupController extends Controller
{
    private $signupService;
    private $welcomeService;

    public function __construct($id, $module, SignupService $signupService, WelcomeUserService $welcomeService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->signupService = $signupService;
        $this->welcomeService = $welcomeService;
    }

    public function actionRequest(Request $request)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $signupForm = new SignupForm();
        if ($signupForm->load($request->post()) && $signupForm->validate()) {
            try {
                $this->signupService->signupWithConfirmEmail(User::createUsername(), $signupForm->email, $signupForm->password);
                Yii::$app->session->setFlash('success', 'Проверьте свой адрес электронной почты, чтобы подтвердить регистрацию');
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }

        return $this->render('request', [
            'formModel' => $signupForm,
        ]);
    }

    public function actionSignupConfirm($token)
    {
        $model = new SignupForm();
        $user = null;
        try {
            $user = $model->confirmation($token);
            Yii::$app->session->setFlash('success', 'Вы успешно подтвердили свою регистрацию');
        }
        catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        if ($user !== null) {
            try {
                $this->welcomeService->afterUserSignup($user);
            }
            catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
            }
        }
        return $this->redirect(['profile/update']);
    }

    public function actionEmail()
    {
        $session = Yii::$app->session;
        $authHandler = $session->get(Auth::AUTH_SESSION_KEY);
        if ($authHandler === null) {
            return $this->goHome();
        }

        $model = new EmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $username = $authHandler['username'];
            $password = Yii::$app->security->generateRandomString(6);

            try {
                $this->signupService->signup($username, $model->email, $password);
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                $session->setFlash('error', $ex->getMessage());
            }

            $user = User::findByUsername($username);
            if ($user !== null) {

                $auth = Auth::create($user->id, $authHandler['source'], (string)$authHandler['source_id']);
                $auth->save();

                try {
                    $this->signupService->sentEmailConfirm($user);
                    Yii::$app->session->setFlash('success', 'Проверьте свой адрес электронной почты, чтобы подтвердить регистрацию');
                } catch (Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                    $session->setFlash('error', 'Ошибка при отправке письма с подтверждением регистрации на сайте');
                }
            }

            $session->remove(Auth::AUTH_SESSION_KEY);
            return $this->goHome();
        }

        return $this->render('email', [
            'model' => $model,
        ]);
    }
}
