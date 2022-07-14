<?php

namespace frontend\models\auth;

use common\models\User;
use yii\base\Model;

class CreateUserForm extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules(): array
    {
        return [
            [['username', 'email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Пользователь с таким email уже существует'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }
}
