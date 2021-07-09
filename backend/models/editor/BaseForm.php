<?php

namespace backend\models\editor;

use common\models\StorySlide;
use yii\base\Model;

class BaseForm extends Model
{

    public $story_id;
    public $slide_id;
    public $block_id;
    public $left;
    public $top;
    public $width;
    public $height;
    public $view;
    public $action;

    public function rules()
    {
        return [
            [['slide_id', 'story_id'], 'integer'],
            [['block_id', 'left', 'top', 'width', 'height'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'left' => 'Левый отступ',
            'top' => 'Верхний отступ',
            'width' => 'Ширина',
            'height' => 'Высота',
        ];
    }

    public function afterCreate(StorySlide $slideModel): void
    {
        //
    }

    public function afterUpdate(StorySlide $slideModel): void
    {
        //
    }
}
