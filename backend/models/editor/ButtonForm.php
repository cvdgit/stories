<?php


namespace backend\models\editor;


class ButtonForm extends TextForm
{

    public $url;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['url'], 'url'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'url' => 'URL',
            'text' => 'Заголовок',
        ]);
        return $labels;
    }

}