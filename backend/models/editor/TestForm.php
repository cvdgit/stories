<?php

namespace backend\models\editor;

class TestForm extends TextForm
{

    public $test_id;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['test_id'], 'required'],
            [['test_id'], 'integer'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'text' => 'Заголовок',
            'test_id' => 'Тест',
        ]);
    }
}
