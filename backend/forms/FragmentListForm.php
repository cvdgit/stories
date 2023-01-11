<?php

declare(strict_types=1);

namespace backend\forms;

use yii\base\Model;

class FragmentListForm extends Model
{
    public $name;
    public $keywords;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
            ['keywords', 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'keywords' => 'Ключевые слова',
        ];
    }
}
