<?php

namespace modules\edu\forms\admin;

use yii\base\Model;

class ClassForm extends Model
{

    public $name;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
        ];
    }
}
