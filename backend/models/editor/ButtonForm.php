<?php

namespace backend\models\editor;

class ButtonForm extends TextForm
{

    public $url;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['url', 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'url' => 'URL',
            'text' => 'Заголовок',
        ]);
    }
}
