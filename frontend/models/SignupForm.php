<?php

declare(strict_types=1);

namespace frontend\models;

use DomainException;
use RuntimeException;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $email;
    public $password;
    public $agree;
    public $captcha;

    public function rules(): array
    {
        return [
            [['email', 'password', 'agree', 'captcha'], 'required'],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Пользователь с таким email уже существует'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['agree', 'required', 'requiredValue' => 1, 'message' => 'Обязательно для заполнения'],
            ['agree', 'boolean'],
            ['captcha', 'captcha', 'captchaAction' => '/signup/captcha'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
            'agree' => 'Я принимаю пользовательское соглашение',
            'captcha' => 'Решите пример'
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            throw new DomainException('Signup model is not valid');
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_WAIT;
        $user->group = User::GROUP_AUTHOR;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailConfirmToken();

        if (!$user->save(false)) {
            throw new DomainException('Signup user save error');
        }

        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole('user');
        $auth->assign($authorRole, $user->getId());

        return $user;
    }

    /**
     * @param $token
     * @return User|null
     */
    public function confirmation($token)
    {
        if (empty($token)) {
            throw new DomainException('Empty confirm token.');
        }

        $user = User::findOne(['email_confirm_token' => $token]);
        if (!$user) {
            throw new DomainException('User is not found.');
        }

        $user->email_confirm_token = null;
        $user->status = User::STATUS_ACTIVE;
        if (!$user->save()) {
            throw new RuntimeException('Saving error.');
        }

        if (!Yii::$app->getUser()->login($user)) {
            throw new RuntimeException('Error authentication.');
        }

        return $user;
    }
}
