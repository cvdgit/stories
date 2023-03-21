<?php

declare(strict_types=1);

namespace common\fixtures;

use yii\test\ActiveFixture;

class ScheduleItemFixture extends ActiveFixture
{
    public $tableName = 'schedule_item';
    public $dataFile = __DIR__ . '/data/schedule_item.php';
    public $depends = [
        ScheduleFixture::class,
    ];
}
