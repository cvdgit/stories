<?php

namespace backend\models\question\input;

use backend\models\question\QuestionType;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use yii\base\Model;

class CreateInputQuestionForm extends Model
{

    public $question_name;
    public $correct_answer_name;

    private $transactionManager;

    public function __construct($config = [])
    {
        $this->transactionManager = new TransactionManager();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['question_name', 'correct_answer_name'], 'required'],
            [['question_name', 'correct_answer_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'question_name' => 'Вопрос',
            'correct_answer_name' => 'Правильный ответ',
        ];
    }

    /**
     * @throws \Exception
     */
    public function createQuestion(int $testId): void
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateInputQuestionForm not valid');
        }
        $this->transactionManager->wrap(function() use ($testId) {
            $questionModel = StoryTestQuestion::create($testId, $this->question_name, QuestionType::ANSWER_INPUT);
            if (!$questionModel->save()) {
                throw new \DomainException('StoryTestQuestion save exception');
            }
            $answerModel = StoryTestAnswer::create($questionModel->id, $this->correct_answer_name, StoryTestAnswer::CORRECT_ANSWER);
            if (!$answerModel->save()) {
                throw new \DomainException('StoryTestAnswer save exception');
            }
        });
    }
}