<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\StoryTestAnswer;
use yii\test\ActiveFixture;

class StoryTestAnswerFixture extends ActiveFixture
{
    public $modelClass = StoryTestAnswer::class;
    public $dataFile = __DIR__ . '/data/story_test_answer.php';
    public $depends = [
        StoryTestQuestionFixture::class,
    ];
}
