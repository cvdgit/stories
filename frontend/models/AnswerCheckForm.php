<?php

namespace frontend\models;

use yii\base\Model;

class AnswerCheckForm extends Model
{

    public $question_id;
    public $answer;

    public function rules(): array
    {
        return [
            ['question_id', 'integer'],
            ['answer', 'string', 'max' => 255],
        ];
    }
}
