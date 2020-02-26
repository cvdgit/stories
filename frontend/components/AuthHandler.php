<?php


namespace frontend\components;

use common\helpers\Translit;
use Exception;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use common\models\Auth;
use common\models\User;
use common\services\auth\AuthService;
use common\services\auth\SignupService;
use common\services\TransactionManager;


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

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->authService = new AuthService();
        $this->transactionManager = new TransactionManager();
        $this->signupService = new SignupService(new TransactionManager());
    }

    public function handle(): void
    {
        $attributes = $this->client->getUserAttributes();
        die(print_r($attributes));
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');
        $username = ArrayHelper::getValue($attributes, 'login');
        if (empty($username)) {
            $username = ArrayHelper::getValue($attributes, 'screen_name');
        }
        if (empty($username)) {
            $username = ArrayHelper::getValue($attributes, 'name');
        }

        $username = Translit::translit($username);
        $username = mb_strtolower($username);
        $username = strtr($username, ['-' => '_']);

        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /* @var User $user */
                $user = $auth->user;
                // $this->updateUserInfo($user);
                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
            } else { // signup
                if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', "Пользователь, с указанным в аккаунте {client} email уже существует.", ['client' => $this->client->getTitle()]),
                    ]);
                } else {

                    $password = Yii::$app->security->generateRandomString(6);
                    $this->signupService->signup($username, $email, $password);

                    /** @var User $user */
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
                        $this->signupService->sendWelcomeEmail($user);
                    }
                    catch (Exception $e) {
                        Yii::$app->errorHandler->logException($e);
                    }
                    $this->signupService->addJob($user->id);
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
                    /** @var User $user */
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