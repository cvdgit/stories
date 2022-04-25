<?php

namespace backend\components\course;

use yii\base\Model;

class LessonCreateForm extends Model
{

    public $course_id;
    public $insert_position;
    public $lesson_order;

    public function rules(): array
    {
        return [
            [['course_id', 'lesson_order'], 'integer'],
            ['insert_position', 'string'],
        ];
    }
}
