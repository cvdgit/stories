<?php

declare(strict_types=1);

namespace modules\edu\forms\teacher;

use yii\base\Model;

class ParentInviteForm extends Model
{
    public $email;

    public function rules(): array
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
        ];
    }
}
