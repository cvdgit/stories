<?php

namespace frontend\components\auth;

use common\helpers\Translit;
use common\services\UserPaymentService;
use common\services\WelcomeUserService;
use DomainException;
use Exception;
use frontend\models\auth\AuthUserForm;
use frontend\models\SignupForm;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use common\models\Auth;
use common\models\User;
use common\services\auth\AuthService;
use common\services\auth\SignupService;
use common\services\TransactionManager;
use yii\helpers\Url;


/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{

    /**
     * @var ClientInterface
     */
    private $client;

    private $authService;
    private $transactionManager;
    private $signupService;
    protected $welcomeUserService;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        if (empty($email)) {
            $email = ArrayHelper::getValue($attributes, 'default_email');
        }
        $id = ArrayHelper::getValue($attributes, 'id');
        $username = ArrayHelper::getValue($attributes, 'login');
        if (empty($username)) {
            $username = ArrayHelper::getValue($attributes, 'screen_name');
        }
        if (empty($username)) {
            $username = ArrayHelper::getValue($attributes, 'name');
        }

        return new AuthUserForm([
            'id' => $id,
            'username' => $username,
            'email' => $email,
        ]);

        /*
        $username = Translit::translit($username);
        $username = mb_strtolower($username);
        $username = strtr($username, ['-' => '_']);

        if (empty($email)) {
            Yii::$app->session->set(Auth::AUTH_SESSION_KEY, [
                'source' => $this->client->getId(),
                'source_id' => $id,
                'username' => $username,
            ]);
            Yii::$app->response->redirect(Url::to('signup/email'));
        }

        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login

                $user = $auth->user;
                // $this->updateUserInfo($user);
                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
            } else { // signup

                if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                    Yii::$app->getSession()->setFlash('error', 'Пользователь, с указанным в аккаунте ' . $this->client->getTitle() . ' email уже существует.');
                } else {

                    $password = Yii::$app->security->generateRandomString(6);
                    $form = new SignupForm([
                        'email' => $email,
                        'password' => $password,
                        'agree' => 1,
                    ]);

                    $this->signupService->signup($username, $email, $password);

                    $user = User::findByUsername($username);
                    $user->status = User::STATUS_ACTIVE;
                    $user->save(false, ['status']);

                    $this->transactionManager->wrap(function() use ($user, $id) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $this->client->getId(),
                            'source_id' => (string)$id,
                        ]);
                        if ($auth->save()) {
                            Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                        } else {
                            Yii::$app->getSession()->setFlash('error', [
                                Yii::t('app', 'Unable to save {client} account: {errors}', [
                                    'client' => $this->client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ]),
                            ]);
                        }
                    });

                    try {
                        $this->welcomeUserService->afterUserSignup($user);
                    }
                    catch (Exception $e) {
                        Yii::$app->errorHandler->logException($e);
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$attributes['id'],
                ]);
                if ($auth->save()) {
                    $user = $auth->user;
                    //$this->updateUserInfo($user);
                    Yii::$app->getSession()->setFlash('success', [
                        Yii::t('app', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ]),
                    ]);
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ]),
                    ]);
                }
            } else { // there's existing auth
                Yii::$app->getSession()->setFlash('error', [
                    Yii::t('app',
                        'Unable to link {client} account. There is another user using it.',
                        ['client' => $this->client->getTitle()]),
                ]);
            }
        }
        */
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        //$attributes = $this->client->getUserAttributes();
        //$github = ArrayHelper::getValue($attributes, 'login');
        //if ($user->github === null && $github) {
        //    $user->github = $github;
        //    $user->save();
        //}
    }

}
