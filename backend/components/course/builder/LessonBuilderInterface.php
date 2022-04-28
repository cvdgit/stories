<?php

namespace backend\components\course\builder;

use backend\components\course\LessonForm;

interface LessonBuilderInterface
{
    public function createBlocksLesson(string $uuid, string $name, int $order, int $id = null): LessonForm;
    public function addSlide(LessonForm $lesson, int $slideId, string $data, int $order): void;
    public function addQuiz(LessonForm $lesson, int $slideId, int $order, int $quizId, string $quizName, string $blockId = null): void;
    public function createQuizLesson(string $uuid, string $name, int $order, int $id = null): LessonForm;
}
