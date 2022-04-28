<?php

namespace backend\components\course\builder;

interface LessonCollectionInterface
{
    public function getLessons(): array;
    public function addLesson($lesson): void;
}
