<?php

namespace backend\components\training\collection;

use backend\components\training\base\BaseQuestion;
use backend\components\training\collection\build\Base;
use backend\components\training\collection\build\Region;
use backend\components\training\collection\build\Sequence;
use common\models\StoryTestQuestion;

class TestCollection extends BaseCollection
{

    public function createQuestion($questionData, $stars)
    {
        /** @var StoryTestQuestion $questionData */
        if ($questionData->typeIsRegion()) {
            $question = (new Region($questionData, $stars))->build();
        }
        else if ($questionData->typeIsSequence()) {
            $question = (new Sequence($questionData, $stars))->build();
        }
        else {
            $question = (new Base($questionData, $stars))->build();
        }

        /** @var BaseQuestion $question */
        if (count($questionData->storySlides) > 0) {
            $question->setHaveSlides(true);
        }

        return $question;
    }

}
