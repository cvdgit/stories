<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use yii\helpers\ArrayHelper;

class UserCreateForm extends Model
{

    public $email;
    public $password;
    public $role;

    public function rules(): array
    {
        return [
            [['email', 'role'], 'required'],
            ['email', 'email'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'unique', 'targetClass' => User::class],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    public function rolesList(): array
    {
        return ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
    }
}