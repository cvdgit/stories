<?php

declare(strict_types=1);

namespace common\fixtures;

use yii\test\ActiveFixture;

class StoryCategoryFixture extends ActiveFixture
{
    public $tableName = 'story_category';
    public $dataFile = __DIR__ . '/data/story_category.php';
    public $depends = [
        StoryFixture::class,
        CategoryFixture::class,
    ];
}
