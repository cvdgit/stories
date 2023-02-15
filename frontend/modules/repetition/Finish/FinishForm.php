<?php

declare(strict_types=1);

namespace frontend\modules\repetition\Finish;

use yii\base\Model;

class FinishForm extends Model
{
    public $test_id;
    public $student_id;

    public function rules(): array
    {
        return [
            [['test_id', 'student_id'], 'integer'],
        ];
    }
}
