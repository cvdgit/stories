<?php

declare(strict_types=1);

namespace modules\edu\Story;

use yii\base\Model;

class AddStoryForm extends Model
{
    public $class_id;
    public $class_program_id;
    public $topic_id;
    public $lesson_id;
    public $story_id;

    public function rules(): array
    {
        return [
            [['class_id', 'class_program_id', 'topic_id', 'lesson_id'], 'required'],
            [['class_id', 'class_program_id', 'topic_id', 'lesson_id', 'story_id'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'class_id' => 'Класс',
            'class_program_id' => 'Программа',
            'topic_id' => 'Тема',
            'lesson_id' => 'Урок',
        ];
    }
}
