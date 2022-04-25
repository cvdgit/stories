<?php

namespace backend\components\course;

use yii\base\Model;

class LessonOrderForm extends Model
{

    public $lesson_id;
    public $lesson_order;

    public function rules(): array
    {
        return [
            [['lesson_id', 'lesson_order'], 'integer'],
        ];
    }
}
