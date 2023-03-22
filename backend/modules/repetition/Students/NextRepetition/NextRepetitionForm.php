<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students\NextRepetition;

use yii\base\Model;

class NextRepetitionForm extends Model
{
    public $test_id;
    public $student_id;

    public function rules(): array
    {
        return [
            [['test_id', 'student_id'], 'required'],
            [['test_id', 'student_id'], 'integer'],
        ];
    }
}
