<?php

namespace backend\models\editor;

class TransitionForm extends TextForm
{
    public $transition_story_id;
    public $slides;
    public $back_to_next_slide;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['transition_story_id'], 'required'],
            [['transition_story_id', 'back_to_next_slide'], 'integer'],
            ['slides', 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'text' => 'Заголовок',
            'transition_story_id' => 'Перейти к истории',
            'slides' => 'Слайды',
            'back_to_next_slide' => 'Возврат на текущий слайд',
        ]);
    }
}
