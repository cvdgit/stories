<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule\Update;

use backend\modules\repetition\Schedule\ScheduleForm;
use backend\modules\repetition\Schedule\ScheduleItemForm;

class UpdateScheduleCommand
{
    /** @var string */
    private $schedule;
    /** @var array<int, int> */
    private $hours;

    /**
     * @param ScheduleForm $schedule
     * @param ScheduleItemForm $items
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

    public function getScheduleId(): int
    {
        return $this->schedule->getId();
    }
}
