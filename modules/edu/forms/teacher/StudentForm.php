<?php

declare(strict_types=1);


namespace modules\edu\forms\teacher;

use yii\base\Model;

class StudentForm extends Model
{

    public $name;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            ['name', 'string', 'max' => 50],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
        ];
    }
}
