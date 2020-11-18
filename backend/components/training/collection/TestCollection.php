<?php

namespace backend\components\training\collection;

use backend\components\training\collection\build\Base;
use backend\components\training\collection\build\Region;
use common\models\StoryTestQuestion;

class TestCollection extends BaseCollection
{

    public function createQuestion(int $testID, $questionData, $stars)
    {
        /** @var StoryTestQuestion $questionData */
        if ($questionData->typeIsRegion()) {
            return (new Region($questionData, $stars))->build();
        }
        return (new Base($questionData, $stars))->build();
    }

}
