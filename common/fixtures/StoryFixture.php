<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\Story;
use yii\test\ActiveFixture;

class StoryFixture extends ActiveFixture
{
    public $modelClass = Story::class;
    public $dataFile = __DIR__ . '/data/story.php';
    public $depends = [
        CategoryFixture::class,
    ];
}
