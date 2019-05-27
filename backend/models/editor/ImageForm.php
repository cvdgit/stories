<?php


namespace backend\models\editor;


class ImageForm extends BaseForm
{

    public $image;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['image', 'required'],
            ['image', 'image', 'maxSize' => 50 * 1024 * 1024],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        return [
            'image' => 'Изображение на слайде',
        ];
    }

}