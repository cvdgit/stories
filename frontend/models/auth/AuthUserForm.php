<?php

namespace frontend\models\auth;

use yii\base\Model;

class AuthUserForm extends Model
{

    public $id;
    public $username;
    public $email;

    public function rules(): array
    {
        return [
            [['id'], 'required'],
            ['email', 'email'],
            ['username', 'string'],
        ];
    }
}
