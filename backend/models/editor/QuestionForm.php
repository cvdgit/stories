<?php

namespace backend\models\editor;

class QuestionForm extends BaseForm
{

    public $test_id;
    public $required;

    public $content;

    public function rules(): array
    {
        return array_merge([
            [['test_id', 'required'], 'integer'],
        ], parent::rules());
    }

    public function attributeLabels(): array
    {
        return array_merge([
            'test_id' => 'Тест',
            'required' => 'Тест обязателен для прохождения',
        ], parent::attributeLabels());
    }

}