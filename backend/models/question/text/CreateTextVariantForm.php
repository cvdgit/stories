<?php

namespace backend\models\question\text;

use yii\base\Model;

class CreateTextVariantForm extends Model
{

    public $text;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['text', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'text' => 'Текст',
        ];
    }
}