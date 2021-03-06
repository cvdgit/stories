<?php


namespace common\services\auth;


use common\models\LoginForm;
use common\models\User;
use DomainException;

class AuthService
{

    public function auth(LoginForm $form)
    {
        $user = User::findByUsernameOrEmail($form->username);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new DomainException('Неверное имя пользователя или пароль');
        }
        return $user;
    }

}