<?php

namespace backend\components\course;

use yii\base\Model;

class LessonDeleteForm extends Model
{

    public $lesson_id;

    public function rules(): array
    {
        return [
            ['lesson_id', 'integer'],
        ];
    }
}
