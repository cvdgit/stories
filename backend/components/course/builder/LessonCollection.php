<?php

namespace backend\components\course\builder;

class LessonCollection implements LessonCollectionInterface
{

    private $lessons = [];

    public function getLessons(): array
    {
        return $this->lessons;
    }

    public function addLesson($lesson): void
    {
        $this->lessons[] = $lesson;
    }
}
