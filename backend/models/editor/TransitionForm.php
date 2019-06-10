<?php


namespace backend\models\editor;


class TransitionForm extends TextForm
{
    public $transition_story_id;
    public $slides;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['transition_story_id'], 'required'],
            [['transition_story_id'], 'integer'],
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
        ]);
        return $labels;
    }
}