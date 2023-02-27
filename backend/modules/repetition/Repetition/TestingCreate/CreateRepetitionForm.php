<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingCreate;

use yii\base\Model;

class CreateRepetitionForm extends Model
{
    public $test_id;
    public $test_name;
    public $student_id;
    public $schedule_id;

    public function rules(): array
    {
        return [
            [['test_id', 'student_id', 'schedule_id'], 'required'],
            [['test_id', 'student_id', 'schedule_id'], 'integer'],
            ['test_name', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'test_name' => 'Тест',
            'student_id' => 'Ученик',
            'schedule_id' => 'Расписание',
        ];
    }
}
