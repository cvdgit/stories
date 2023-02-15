<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule\CreateSchedule;

use backend\modules\repetition\Schedule\ScheduleForm;
use backend\modules\repetition\Schedule\ScheduleItemForm;

class CreateScheduleCommand
{
    /** @var string */
    private $schedule;
    /** @var array<int, int> */
    private $hours;

    /**
     * @param ScheduleForm $schedule
     * @param list<ScheduleItemForm> $items
     */
    public function __construct(ScheduleForm $schedule, array $items)
    {
        $this->schedule = $schedule;
        $this->hours = array_map(static function(ScheduleItemForm $item) {
            return $item->hours;
        }, $items);
    }

    public function getScheduleName(): string
    {
        return $this->schedule->name;
    }

    /**
     * @return array{int, int}
     */
    public function getScheduleHours(): array
    {
        return $this->hours;
    }
}
