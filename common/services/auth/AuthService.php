<?php

namespace common\services\auth;

use common\components\ModelDomainException;
use common\helpers\UserHelper;
use common\models\Auth;
use common\models\LoginForm;
use common\models\User;
use DomainException;
use frontend\components\ChainUrlMatcher;
use frontend\components\EduStoryUrlMatcher;
use frontend\components\NoEmailException;
use frontend\components\StoryUrlMatcher;
use frontend\components\UserAlreadyExistsException;
use frontend\models\auth\AuthUserForm;
use frontend\models\auth\CreateUserForm;
use modules\edu\components\EduSessionManager;
use modules\edu\models\StudentLogin;
use Yii;

class AuthService
{

    private $signupService;
    private $eduSessionManager;

    public function __construct(SignupService $signupService, EduSessionManager $eduSessionManager)
    {
        $this->signupService = $signupService;
        $this->eduSessionManager = $eduSessionManager;
    }

    public function auth(LoginForm $form): array
    {
        if (!$form->validate()) {
            throw new DomainException('Некорректные данные');
        }

        $login = $form->email;

        $returnRoute = ['/site/index'];
        $user = User::findByEmail($login);
        if ($user === null) {

            $studentLogin = StudentLogin::findLogin($login, $form->password);
            if (!$studentLogin) {
                throw new DomainException('Неверное имя пользователя или пароль');
            }

            $user = $studentLogin->student->user;
            $returnRoute = ['/edu/student/index'];

            $this->eduSessionManager->switch($user->id, $studentLogin->student->id);
        }
        else {
            if (!$user->isActive() || !$user->validatePassword($form->password)) {
                throw new DomainException('Неверное имя пользователя или пароль');
            }
        }

        Yii::$app->user->login($user, $form->rememberMe ? Yii::$app->params['user.rememberMeDuration'] : 0);

        return $returnRoute;
    }

    public function getBackRoute(string $returnUrl = null, string $referrerUrl = null): ?array
    {
        $url = $referrerUrl ?? null;
        if ($url === null && $returnUrl !== null) {
            $url = $returnUrl;
        }

        if ($url !== null) {

            $matcher = new ChainUrlMatcher(...[
                new StoryUrlMatcher(),
                new EduStoryUrlMatcher(),
            ]);

            if (($result = $matcher->match($url)) !== null) {
                return [
                    'url' => $url,
                    'match' => $result,
                ];
            }
        }
        return null;
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
