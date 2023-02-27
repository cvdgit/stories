<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\StoryStart;

use yii\base\Model;

class StartRepetitionForm extends Model
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
