<?php


namespace backend\models\editor;


class TestForm extends TextForm
{

    public $test_id;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['test_id'], 'required'],
            [['test_id'], 'integer'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'text' => 'Заголовок',
            'test_id' => 'Тест',
        ]);
        return $labels;
    }

}