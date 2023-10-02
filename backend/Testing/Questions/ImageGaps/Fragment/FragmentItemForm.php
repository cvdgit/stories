<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Fragment;

use yii\base\Model;

class FragmentItemForm extends Model
{
    public $name;
    public $correct;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string'],
            ['correct', 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Ответ',
            'correct' => 'Правильный',
        ];
    }
}
