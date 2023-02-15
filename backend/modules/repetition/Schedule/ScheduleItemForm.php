<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule;

use yii\base\Model;

class ScheduleItemForm extends Model
{
    public $hours;
    public $id;

    public function rules(): array
    {
        return [
            ['hours', 'required'],
            ['hours', 'integer', 'min' => 1],
            ['id', 'integer'],
        ];
    }
}
