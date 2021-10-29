<?php

namespace backend\services;

use common\models\StoryTest;
use common\models\test\SourceType;
use common\models\test\TestTemplateParts;

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

    private function formatTestString(string $str = null, array $parts = []): ?string
    {
        if (empty($str)) {
            return null;
        }
        if (count($parts) === 0) {
            return $str;
        }
        return str_replace(array_keys($parts), array_values($parts), $str);
    }

    public function createFromTemplate(int $testTemplateId, string $namePart): StoryTest
    {
        $templateModel = StoryTest::findOne($testTemplateId);
        $testModel = StoryTest::createFromTemplate($templateModel);
        $parts = [TestTemplateParts::WORDLIST_NAME => $namePart];
        $testModel->title = $this->formatTestString($templateModel->header, $parts);
        $testModel->header = $this->formatTestString($templateModel->header, $parts);
        $testModel->description_text = $this->formatTestString($templateModel->description_text, $parts);
        return $testModel;
    }
}
