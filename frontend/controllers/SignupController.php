<?php


namespace frontend\controllers;


use common\models\Auth;
use common\models\User;
use Exception;
use frontend\models\EmailForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use frontend\models\SignupForm;
use common\services\auth\SignupService;


class SignupController extends Controller
{

    protected $service;

    public function __construct($id, $module, SignupService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionRequest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $model = new SignupForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                try {
                    $this->service->signup($model->username, $model->email, $model->password);
                }
                catch (Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                    return ['success' => false, 'message' => [$ex->getMessage()]];
                }
                $user = User::findByUsername($model->username);
                if ($user !== null) {
                    try {
                        $this->service->sentEmailConfirm($user);
                        return ['success' => true, 'message' => ['Проверьте свой адрес электронной почты, чтобы подтвердить регистрацию']];
                    } catch (Exception $ex) {
                        Yii::$app->errorHandler->logException($ex);
                        return ['success' => false, 'message' => ['Ошибка при отправке письма с подтверждением регистрации на сайте']];
                    }
                }
            }
            else {
                return ['success' => false, 'message' => $model->errors];
            }
        }
        return ['success' => false, 'message' => ['']];
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
                $this->service->sendWelcomeEmail($user);
            }
            catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
            }
            $this->service->addJob($user->id);
        }
        return $this->goHome();
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
                $this->service->signup($username, $model->email, $password);
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                $session->setFlash('error', $ex->getMessage());
            }

            $user = User::findByUsername($username);
            if ($user !== null) {

                $auth = Auth::create($user->id, $authHandler['source'], $authHandler['source_id']);
                $auth->save();

                try {
                    $this->service->sentEmailConfirm($user);
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