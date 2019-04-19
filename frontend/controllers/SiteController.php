<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\components\AuthHandler;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            /*
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            */
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
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
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
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
            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->login()) {
                    return ['success' => true, 'message' => ''];
                }
                else {
                    return ['success' => false, 'message' => ['']];
                }
            }
            else {
                return ['success' => false, 'message' => $model->errors];
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

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        /*
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $model->sendEmail(Yii::$app->params['adminEmail']);
                Yii::$app->session->setFlash('success', 'Благодарим Вас за обращение к нам. Мы ответим вам как можно скорее.');
            }
            catch(\Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', 'При отправке вашего сообщения произошла ошибка.');
            }
            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
        */
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $model = new ContactForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                try {
                    $model->sendEmail(Yii::$app->params['adminEmail']);
                    return ['success' => true, 'message' => 'Благодарим Вас за обращение к нам. Мы ответим вам как можно скорее'];
                }
                catch (\Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                    return ['success' => false, 'message' => ['При отправке вашего сообщения произошла ошибка']];
                }
            }
            else {
                return ['success' => false, 'message' => $model->errors];
            }
        }
        return ['success' => false, 'message' => ['']];
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $model = new SignupForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                try {
                    $user = $model->signup();
                    try {
                        $model->sentEmailConfirm($user);
                        return ['success' => true, 'message' => 'Проверьте свой адрес электронной почты, чтобы подтвердить регистрацию'];
                    }
                    catch (\Exception $ex) {
                        Yii::$app->errorHandler->logException($ex);
                        return ['success' => false, 'message' => ['Ошибка при отправке письма с подтверждением регистрации на сайте']];
                    }
                }
                catch (\Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                    return ['success' => false, 'message' => ['Произошла ошибка при регистрации пользователя']];
                }
            }
            else {
                return ['success' => false, 'message' => $model->errors];
            }
        }
        return ['success' => false, 'message' => ['']];
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionSignupConfirm($token)
    {
        $model = new SignupForm();
        try {
            $model->confirmation($token);
            Yii::$app->session->setFlash('success', 'You have successfully confirmed your registration.');
        }
        catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->goHome();
    }

    public function actionPolicy()
    {
        return $this->render('policy');
    }
}
