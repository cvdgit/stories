<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\StoryTest;
use yii\test\ActiveFixture;

class StoryTestFixture extends ActiveFixture
{
    public $modelClass = StoryTest::class;
    public $dataFile = __DIR__ . '/data/story_test.php';
    public $depends = [
        UserFixture::class,
    ];
}
