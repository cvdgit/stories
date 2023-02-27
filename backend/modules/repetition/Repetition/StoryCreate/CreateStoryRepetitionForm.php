<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\StoryCreate;

use yii\base\Model;

class CreateStoryRepetitionForm extends Model
{
    public $student_id;
    public $schedule_id;

    public function rules(): array
    {
        return [
            [['student_id', 'schedule_id'], 'required'],
            [['student_id', 'schedule_id'], 'integer'],
        ];
    }
}
