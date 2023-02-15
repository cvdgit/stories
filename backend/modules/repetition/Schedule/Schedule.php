<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property int $created_at [int(11)]
 *
 * @property-read ScheduleItem[] $scheduleItems
 */
class Schedule extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'schedule';
    }

    public function getScheduleItems(): ActiveQuery
    {
        return $this->hasMany(ScheduleItem::class, ['schedule_id' => 'id'])
            ->orderBy(['hours' => SORT_ASC]);
    }
}
