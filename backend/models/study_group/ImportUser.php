<?php

namespace backend\models\study_group;

use yii\base\Model;

class ImportUser extends Model
{

    public $email;
    public $lastname;
    public $firstname;

    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            [['lastname', 'firstname'], 'string', 'max' => 255],
        ];
    }
}