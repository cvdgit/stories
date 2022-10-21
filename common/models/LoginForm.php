<?php

declare(strict_types=1);

namespace common\models;

use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;
    public $returnUrl;

    public function __construct(string $returnUrl = null, $config = [])
    {
        parent::__construct($config);
        $this->returnUrl = $returnUrl;
    }

    public function rules(): array
    {
        return [
            ['email', 'trim'],
            [['email', 'password'], 'required'],
            [['email'], 'string', 'max' => 255],
            ['rememberMe', 'boolean'],
            ['returnUrl', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'Логин',
            'rememberMe' => 'Запомнить',
            'password' => 'Пароль',
        ];
    }
}
