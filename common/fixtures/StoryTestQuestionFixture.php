<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\StoryTestQuestion;
use yii\test\ActiveFixture;

class StoryTestQuestionFixture extends ActiveFixture
{
    public $modelClass = StoryTestQuestion::class;
    public $dataFile = __DIR__ . '/data/story_test_question.php';
    public $depends = [
        StoryTestFixture::class,
    ];
}
