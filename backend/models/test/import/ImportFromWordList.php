<?php

namespace backend\models\test\import;

use backend\models\question\QuestionType;
use common\models\TestWordList;
use yii\base\Model;

class ImportFromWordList extends Model
{

    public $word_list_id;
    public $number_answers;
    public $question_type;

    public function rules(): array
    {
        return [
            [['word_list_id', 'question_type'], 'required'],
            [['word_list_id', 'question_type'], 'integer'],
            ['number_answers', 'integer', 'max' => 10, 'min' => 1],
            [
                'number_answers',
                'required',
                'when' => function(self $model) {
                    return !$model->isTypeSequence();
                },
                'whenClient' => "function (attribute, value) {
                    return $('#importfromwordlist-question_type').val() === '0';
                }"
            ],
            ['word_list_id', 'exist', 'targetClass' => TestWordList::class, 'targetAttribute' => ['word_list_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'word_list_id' => 'Список слов',
            'number_answers' => 'Количество ответов',
            'question_type' => 'Тип вопросов',
        ];
    }

    public function getQuestionTypes(): array
    {
        return [
            QuestionType::ONE => 'Один ответ',
            QuestionType::SEQUENCE => 'Восстановить последовательность',
        ];
    }

    public function isTypeSequence(): bool
    {
        return (int)$this->question_type === QuestionType::SEQUENCE;
    }
}
