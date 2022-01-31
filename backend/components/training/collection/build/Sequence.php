<?php

namespace backend\components\training\collection\build;

use backend\components\training\base\Answer;
use backend\components\training\local\SequenceQuestion;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;

class Sequence
{

    private $question;
    private $stars;

    public function __construct(StoryTestQuestion $question, $stars)
    {
        $this->question = $question;
        $this->stars = $stars;
    }

    public function build()
    {
        $question = new SequenceQuestion($this->question, $this->stars);
        foreach ($this->question->storyTestAnswers as $answer) {
            /** @var $answer StoryTestAnswer */
            $question->addAnswer(new Answer($answer->id, $answer->name, $answer->answerIsCorrect(), $answer->region_id, $answer->getImageUrl(), $answer->order, $answer->getOrigImageUrl()));
        }
        return $question;
    }

}
