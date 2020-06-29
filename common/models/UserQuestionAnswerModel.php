<?php

namespace common\models;

use yii\base\Model;

class UserQuestionAnswerModel extends Model
{

    public $question_answer_id;
    public $answer_entity_id;
    public $answer_entity_name;

    public function rules()
    {
        return [
            [['question_answer_id', 'answer_entity_id'], 'integer'],
            ['answer_entity_name', 'string'],
        ];
    }

    public function createUserQuestionAnswer()
    {
        if (!$this->validate()) {
            throw new \DomainException('User question answer data is not valid');
        }
        $model = UserQuestionAnswer::create(
            $this->question_answer_id,
            $this->answer_entity_id,
            $this->answer_entity_name
        );
        $model->save();
    }

}