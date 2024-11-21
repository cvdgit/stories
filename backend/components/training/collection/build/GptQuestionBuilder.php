<?php

declare(strict_types=1);

namespace backend\components\training\collection\build;

use backend\components\training\local\GptQuestion;
use common\models\StoryTestQuestion;

class GptQuestionBuilder
{
    private $question;
    private $stars;

    public function __construct(StoryTestQuestion $question, $stars)
    {
        $this->question = $question;
        $this->stars = $stars;
    }

    public function build(): GptQuestion
    {
        return new GptQuestion($this->question, $this->stars);
    }
}
