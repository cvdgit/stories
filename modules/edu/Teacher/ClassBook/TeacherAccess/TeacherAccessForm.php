<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

use yii\base\Model;

class TeacherAccessForm extends Model
{
    public $class_book_id;
    public $teacher_id;

    public function rules(): array
    {
        return [
            [['class_book_id', 'teacher_id'], 'required'],
            [['class_book_id', 'teacher_id'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'teacher_id' => 'Учитель',
        ];
    }
}
