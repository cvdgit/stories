<?php

declare(strict_types=1);

namespace common\fixtures;

use yii\test\ActiveFixture;

class ScheduleFixture extends ActiveFixture
{
    public $tableName = 'schedule';
    public $dataFile = __DIR__ . '/data/schedule.php';
}
