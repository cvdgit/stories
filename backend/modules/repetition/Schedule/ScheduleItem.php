<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule;

use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property int $schedule_id [int(11)]
 * @property bool $hours [tinyint(3)]
 */
class ScheduleItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'schedule_item';
    }
}
