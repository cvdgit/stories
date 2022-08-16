<?php

declare(strict_types=1);

namespace frontend\models;

use yii\base\Model;

class StoryStudentStatForm extends Model
{

    public $story_id;
    public $student_id;
    public $slide_id;
    public $session;

    public function rules(): array
    {
        return [
            [['story_id', 'student_id', 'slide_id', 'session'], 'required'],
            [['story_id', 'student_id', 'slide_id'], 'integer'],
            ['session', 'string', 'max' => 50],
        ];
    }
}
