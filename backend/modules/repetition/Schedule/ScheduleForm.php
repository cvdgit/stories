<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule;

use yii\base\Model;

class ScheduleForm extends Model
{
    public $name;

    /** @var int|null */
    private $id;

    /** @var list<ScheduleItemForm> */
    private $items = [];

    public function __construct(?Schedule $schedule, $config = [])
    {
        parent::__construct($config);
        if ($schedule !== null) {
            $this->id = $schedule->id;
            $this->name = $schedule->name;
            $this->items = array_map(static function(ScheduleItem $item) {
                return new ScheduleItemForm(['hours' => $item->hours, 'id' => $item->id]);
            }, $schedule->scheduleItems);
        }
    }

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название расписания',
        ];
    }

    /**
     * @return ScheduleItemForm[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
