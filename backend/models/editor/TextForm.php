<?php

namespace backend\models\editor;

class TextForm extends BaseForm
{

    public $text;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['text'], 'required'],
            [['text'], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'text' => 'Текст слайда',
        ]);
    }
}
