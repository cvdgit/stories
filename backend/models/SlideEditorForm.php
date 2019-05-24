<?php

namespace backend\models;

use yii;

class SlideEditorForm extends yii\base\Model
{

    public $text;
    public $text_size;
    public $story_id;
    public $slide_index;
    public $image;
    public $button;

    public $linkForm;

    public function init()
    {
        $this->linkForm = new LinkForm();
        parent::init();
    }

    public function rules()
    {
        return [
            [['text'], 'string'],
            ['text_size', 'string'],
            [['story_id', 'slide_index'], 'integer'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 50 * 1024 * 1024],
            ['button', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст слайда',
            'text_size' => 'Размер шрифта',
            'image' => 'Изображение на слайде',
            'button' => 'Кнопка',
        ];
    }

}