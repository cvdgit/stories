<?php

namespace backend\models\user;

use yii\base\Model;

class SelectUserForm extends Model
{

    public $user_id;

    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['user_id', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
        ];
    }
}