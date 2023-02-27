<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingDelete;

use yii\base\Model;

class DeleteRepetitionForm extends Model
{
    public $test_id;
    public $student_id;
    public $schedule_id;

    public function rules(): array
    {
        return [
            [['test_id', 'student_id', 'schedule_id'], 'required'],
            [['test_id', 'student_id', 'schedule_id'], 'integer'],
        ];
    }
}
