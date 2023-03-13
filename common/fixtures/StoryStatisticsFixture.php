<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\StoryStatistics;
use yii\test\ActiveFixture;

class StoryStatisticsFixture extends ActiveFixture
{
    public $modelClass = StoryStatistics::class;
    public $dataFile = __DIR__ . '/data/story_statistics.php';
}
