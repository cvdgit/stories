<?php

namespace frontend\models;

use yii\base\Model;

class AnswerCreateHiddenForm extends Model
{

    public $question_id;
    public $answer;

    public function rules(): array
    {
        return [
            ['answer', 'string', 'max' => 255],
            ['question_id', 'integer'],
        ];
    }
}
