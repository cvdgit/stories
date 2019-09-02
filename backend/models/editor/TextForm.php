<?php


namespace backend\models\editor;


class TextForm extends BaseForm
{

    public $text;
    public $text_size;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            //[['text', 'text_size'], 'required'],
            [['text', 'text_size'], 'string'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'text' => 'Текст слайда',
            'text_size' => 'Размер шрифта',
        ]);
        return $labels;
    }

}