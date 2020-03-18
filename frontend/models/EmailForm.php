<?php


namespace frontend\models;


use common\models\User;
use yii\base\Model;

class EmailForm extends Model
{

    public $email;

    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Пользователь с таким email уже существует'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email'
        ];
    }

}