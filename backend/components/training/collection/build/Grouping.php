<?php

declare(strict_types=1);

namespace backend\components\training\collection\build;

use backend\components\training\base\Answer;
use backend\components\training\local\GroupingQuestion;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;

class Grouping
{
    private $question;
    private $stars;

    public function __construct(StoryTestQuestion $question, $stars)
    {
        $this->question = $question;
        $this->stars = $stars;
    }

    public function build(): GroupingQuestion
    {
        $question = new GroupingQuestion($this->question, $this->stars);
        foreach ($this->question->storyTestAnswers as $answer) {
            /** @var $answer StoryTestAnswer */
            $item = new Answer($answer->id, $answer->name, $answer->answerIsCorrect());
            $item->setDescription($answer->description);
            $question->addAnswer($item);
        }
        return $question;
    }
}
