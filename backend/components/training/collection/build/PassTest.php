<?php

namespace backend\components\training\collection\build;

use backend\components\training\base\Answer;
use backend\components\training\local\PassTestQuestion;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;

class PassTest
{

    private $question;
    private $stars;

    public function __construct(StoryTestQuestion $question, $stars)
    {
        $this->question = $question;
        $this->stars = $stars;
    }

    public function build(): PassTestQuestion
    {
        $question = new PassTestQuestion($this->question, $this->stars);
        foreach ($this->question->storyTestAnswers as $answer) {
            /** @var $answer StoryTestAnswer */
            $question->addAnswer(new Answer($answer->id, $answer->name, $answer->answerIsCorrect()));
        }
        return $question;
    }
}
