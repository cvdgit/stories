<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\StoryStoryTest;
use yii\test\ActiveFixture;

class StoryStoryTestFixture extends ActiveFixture
{
    public $modelClass = StoryStoryTest::class;
    public $dataFile = __DIR__ . '/data/story_story_test.php';
}
