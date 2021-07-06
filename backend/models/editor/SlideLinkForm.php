<?php

namespace backend\models\editor;

use yii\base\Model;

class SlideLinkForm extends Model
{

    public $link_story_id;
    public $link_slide_id;
    public $story_id;
    public $slide_id;

    public function rules()
    {
        return [
            [['link_slide_id', 'link_story_id'], 'required'],
            [['link_slide_id', 'link_story_id', 'story_id', 'slide_id'], 'integer'],
            ['slide_id', 'default', 'value' => null],
        ];
    }

    public function attributeLabels()
    {
        return [
            'link_story_id' => 'История',
            'link_slide_id' => 'Слайд',
        ];
    }
}
