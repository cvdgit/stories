<?php

namespace backend\models\editor;

class ImageFromUrlForm extends ImageForm
{

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['url', 'required'],
            ['url', 'url'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'url' => 'Ссылка на изображение',
        ]);
    }
}
