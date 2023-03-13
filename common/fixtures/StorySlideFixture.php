<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\StorySlide;
use yii\test\ActiveFixture;

class StorySlideFixture extends ActiveFixture
{
    public $modelClass = StorySlide::class;
    public $dataFile = __DIR__ . '/data/story_slide.php';
}
