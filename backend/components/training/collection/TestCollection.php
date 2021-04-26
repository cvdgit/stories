<?php

namespace backend\components\training\collection;

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
            return (new Region($questionData, $stars))->build();
        }
        if ($questionData->typeIsSequence()) {
            return (new Sequence($questionData, $stars))->build();
        }
        return (new Base($questionData, $stars))->build();
    }

}
