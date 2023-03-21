<?php

declare(strict_types=1);

namespace common\fixtures;

use yii\test\ActiveFixture;

class TestRepetitionFixture extends ActiveFixture
{
    public $tableName = 'test_repetition';
    public $dataFile = __DIR__ . '/data/test_repetition.php';
    public $depends = [
        StoryTestFixture::class,
        UserStudentFixture::class,
        ScheduleItemFixture::class,
    ];
}
