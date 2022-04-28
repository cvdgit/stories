<?php

namespace backend\components\course\builder;

use backend\components\course\LessonBlockForm;
use backend\components\course\LessonForm;
use backend\components\course\LessonQuizForm;
use backend\components\course\LessonType;

class LessonBuilder implements LessonBuilderInterface
{

    private function createLesson(int $type, string $uuid, string $name, int $order, int $id = null): LessonForm
    {
        return LessonForm::create($type, $uuid, $name, $order, $id);
    }

    public function createBlocksLesson(string $uuid, string $name, int $order, int $id = null): LessonForm
    {
        return $this->createLesson(LessonType::BLOCKS, $uuid, $name, $order, $id);
    }

    public function addSlide(LessonForm $lesson, int $slideId, string $data, int $order): void
    {
        $lesson->addBlock(LessonBlockForm::create($slideId, $data, $order));
    }

    public function addQuiz(LessonForm $lesson, int $slideId, int $order, int $quizId, string $quizName, string $blockId = null): void
    {
        $lesson->addBlock(LessonQuizForm::create($slideId, '', $order, $quizId, $quizName, $blockId));
    }

    public function createQuizLesson(string $uuid, string $name, int $order, int $id = null): LessonForm
    {
        return $this->createLesson(LessonType::QUIZ, $uuid, $name, $order, $id);
    }
}
