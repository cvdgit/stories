<?php

declare(strict_types=1);

namespace frontend\Quiz\ColumnQuestion;

use yii\base\Model;

class ColumnAbortForm extends Model
{
    public $questionId;
    public $payload;
    public $studentId;

    public function rules(): array
    {
        return [
            [['questionId', 'studentId'], 'required'],
            [['questionId', 'studentId'], 'integer'],
            ['payload', 'safe'],
        ];
    }
}
