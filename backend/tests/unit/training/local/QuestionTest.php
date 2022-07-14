<?php namespace backend\tests\training\local;

use backend\components\training\base\Answer;
use backend\components\training\local\Question;
use backend\models\question\QuestionType;

class QuestionTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testCorrectAnswerNumberWithNoAnswers()
    {
        $question = new Question(
            $id = 1,
            $name = 'Test Question',
            $lastAnswerIsCorrect = true,
            $mixAnswers = 1,
            $type = QuestionType::ONE
        );
        $this->assertEquals($question->getCorrectAnswerNumber(), 0);
    }

    public function testCorrectAnswerNumberWithNoCorrectAnswers()
    {
        $question = new Question(
            $id = 1,
            $name = 'Test Question',
            $lastAnswerIsCorrect = true,
            $mixAnswers = 1,
            $type = QuestionType::ONE
        );

        $answer = new Answer($answerId = 1, $answerName = 'Answer', $correct = false);
        $question->addAnswer($answer);

        $this->assertEquals($question->getCorrectAnswerNumber(), 0);
    }

    public function testCorrectAnswerNumberWithCorrectAnswer()
    {
        $question = new Question(
            $id = 1,
            $name = 'Test Question',
            $lastAnswerIsCorrect = true,
            $mixAnswers = 1,
            $type = QuestionType::ONE
        );

        $answer = new Answer($answerId = 1, $answerName = 'Answer', $correct = true);
        $question->addAnswer($answer);

        $this->assertEquals($question->getCorrectAnswerNumber(), 1);
    }
}
