<?php

namespace backend\components\training\collection\build;

use backend\components\training\base\Answer;
use backend\components\training\local\DefaultQuestion;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;

class Base
{

    private $question;
    private $stars;
    private $testModel;

    public function __construct(StoryTestQuestion $question, $stars, StoryTest $testModel)
    {
        $this->question = $question;
        $this->stars = $stars;
        $this->testModel = $testModel;
    }

    public function build()
    {
        $question = new DefaultQuestion($this->question, $this->stars);
        foreach ($this->question->storyTestAnswers as $answer) {
            /** @var $answer StoryTestAnswer */
            $newAnswer = new Answer($answer->id, $answer->name, $answer->answerIsCorrect(), '', $answer->getImagePath(), null, $answer->getOrigImagePath());
            if ($this->testModel->showAnswersHints()) {
                $newAnswer->setDescription($answer->description);
            }
            $question->addAnswer($newAnswer);
        }
        return $question;
    }
}
