<?php

namespace common\services\auth;

use common\components\ModelDomainException;
use common\helpers\UserHelper;
use common\models\Auth;
use common\models\LoginForm;
use common\models\User;
use DomainException;
use frontend\components\NoEmailException;
use frontend\components\UserAlreadyExistsException;
use frontend\models\auth\AuthUserForm;
use frontend\models\auth\CreateUserForm;
use Yii;

class AuthService
{

    private $signupService;

    public function __construct(SignupService $signupService)
    {
        $this->signupService = $signupService;
    }

    public function auth(LoginForm $form): ?User
    {
        $user = User::findByEmail($form->email);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new DomainException('Неверное имя пользователя или пароль');
        }
        return $user;
    }

    /**
     * @throws NoEmailException
     * @throws UserAlreadyExistsException
     */
    public function socialAuth(string $source, AuthUserForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $authModel = Auth::findAuth($source, $form->id);

        if (Yii::$app->user->isGuest) {

            if ($authModel === null) {

                if (empty($form->email)) {
                    throw new NoEmailException();
                }

                if (User::find()->where(['email' => $form->email])->exists()) {
                    throw new UserAlreadyExistsException();
                }

                $createUserForm = new CreateUserForm([
                    'username' => UserHelper::formatUsername($form->username),
                    'email' => $form->email,
                    'password' => User::createPassword(),
                ]);
                $userModel = $this->signupService->signupSocial($createUserForm);

                $newAuthModel = Auth::create($userModel->id, $source, $form->id);
                if (!$newAuthModel->save()) {
                    throw ModelDomainException::create($newAuthModel);
                }

                $this->loginUser($userModel);
            }
            else {
                $this->loginUser($authModel->user);
            }
        }
        else {

            if ($authModel === null) {

                $newAuthModel = Auth::create(Yii::$app->user->id, $source, $form->id);
                if (!$newAuthModel->save()) {
                    throw ModelDomainException::create($newAuthModel);
                }
            }
            else {
                throw new DomainException('Невозможно связать аккаунт');
            }
        }
    }

    public function loginUser(User $user): void
    {
        Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
    }

    public function noEmailRedirect(string $source, AuthUserForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        Yii::$app->session->set(Auth::AUTH_SESSION_KEY, [
            'source' => $source,
            'source_id' => $form->id,
            'username' => $form->username,
        ]);
        Yii::$app->response->redirect(['signup/email']);
        Yii::$app->end();
    }
}
