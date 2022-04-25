<?php

namespace backend\components\course\builder;

use backend\components\course\LessonForm;

class LessonCollection
{

    private $lessons = [];

    public function getLessons(): array
    {
        return $this->lessons;
    }

    public function addLesson(LessonForm $lesson): void
    {
        $this->lessons[] = $lesson;
    }
}
