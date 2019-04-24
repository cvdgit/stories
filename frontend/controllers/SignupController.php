<?php


namespace frontend\controllers;


use common\models\User;
use Exception;
use RuntimeException;
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
                        return ['success' => true, 'message' => 'Проверьте свой адрес электронной почты, чтобы подтвердить регистрацию'];
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
        try {
            $model->confirmation($token);
            Yii::$app->session->setFlash('success', 'You have successfully confirmed your registration.');
        }
        catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->goHome();
    }

}