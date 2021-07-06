<?php

namespace backend\models;

use yii;

class SourcePowerPointForm extends yii\base\Model
{

    public $storyFile;
    public $slidesNumber;
    public $storyId;

    public function rules()
    {
        return [
            [['storyFile'], 'string', 'max' => 50],
            [['storyId', 'slidesNumber'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'storyFile' => 'Файл PowerPoint',
            'firstSlideTemplate' => 'Особый шаблон первого слайда',
            'lastSlideTemplate' => 'Особый шаблон последнего слайда',
            'originalSizeImages' => 'Исходный размер изображений',
            'slidesNumber' => 'Количество слайдов',
        ];
    }
}
