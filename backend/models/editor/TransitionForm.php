<?php


namespace backend\models\editor;


class TransitionForm extends TextForm
{
    public $transition_story_id;
    public $slides;
    public $back_to_next_slide;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['transition_story_id'], 'required'],
            [['transition_story_id', 'back_to_next_slide'], 'integer'],
            ['slides', 'string'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'text' => 'Заголовок',
            'transition_story_id' => 'Перейти к истории',
            'slides' => 'Слайды',
            'back_to_next_slide' => 'Возврат на текущий слайд',
        ]);
        return $labels;
    }
}