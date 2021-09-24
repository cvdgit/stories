<?php

namespace backend\models\test_template;

use yii\base\Model;

class TestItemForm extends Model
{

    public $template_id;
    public $word_list_processing;

    public function rules()
    {
        return [
            [['template_id', 'word_list_processing'], 'required'],
            [['template_id', 'word_list_processing'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'template_id' => 'Шаблон',
            'word_list_processing' => 'Обработка',
        ];
    }
}
