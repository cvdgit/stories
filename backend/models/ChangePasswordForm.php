<?php


namespace backend\models;


use yii\base\Model;

class ChangePasswordForm extends Model
{

    public $password;

    public function rules()
    {
        return [
            ['password', 'string', 'min' => 6],
        ];
    }

}