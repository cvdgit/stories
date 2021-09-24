<?php

namespace backend\services;

use common\models\StoryTest;
use common\models\test\SourceType;

class StoryTestService
{

    public function createFromWordList(string $title, int $wordListID, int $answerType, int $shuffleWordList, int $strictAnswer): StoryTest
    {
        $model = StoryTest::create($title, $title, '', '', StoryTest::LOCAL, SourceType::LIST);
        $model->word_list_id = $wordListID;
        $model->answer_type = $answerType;
        $model->shuffle_word_list = $shuffleWordList;
        $model->strict_answer = $strictAnswer;
        return $model;
    }

    public function createFromTemplate(int $testTemplateId): StoryTest
    {
        $templateModel = StoryTest::findOne($testTemplateId);
        return StoryTest::createFromTemplate($templateModel);
    }
}
