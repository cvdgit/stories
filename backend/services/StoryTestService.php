<?php

namespace backend\services;

use common\models\StoryTest;
use common\models\test\SourceType;

class StoryTestService
{

    public function createFromWordList(string $title, int $wordListID, int $answerType)
    {
        $model = StoryTest::create($title, $title, '', '', StoryTest::LOCAL, SourceType::LIST);
        $model->word_list_id = $wordListID;
        $model->answer_type = $answerType;
        return $model;
    }

}