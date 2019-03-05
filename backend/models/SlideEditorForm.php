<?php

namespace backend\models;

use yii;

class SlideEditorForm extends yii\base\Model
{

    public $text;
    public $story_id;
    public $slide_index;

    public function rules()
    {
        return [
            [['text'], 'string'],
            [['story_id', 'slide_index'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст слайда',
        ];
    }

}