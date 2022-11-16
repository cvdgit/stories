<?php

declare(strict_types=1);

namespace modules\testing\forms;

use yii\base\Model;

class PoetryForm extends Model
{
    public $name;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            ['name', 'string'],
        ];
    }
}
