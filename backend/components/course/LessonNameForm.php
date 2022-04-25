<?php

namespace backend\components\course;

use yii\base\Model;

class LessonNameForm extends Model
{

    public $lesson_id;
    public $lesson_name;

    public function rules(): array
    {
        return [
            ['lesson_id', 'integer'],
            ['lesson_name', 'string', 'max' => 255],
        ];
    }
}
