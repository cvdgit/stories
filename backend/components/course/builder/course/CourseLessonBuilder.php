<?php

namespace backend\components\course\builder\course;

use backend\components\course\builder\BaseBuilder;
use backend\components\course\builder\LessonCollectionInterface;
use backend\components\SlideWrapper;

class CourseLessonBuilder extends BaseBuilder
{

    public function build(array $models): LessonCollectionInterface
    {
        foreach ($models as $lessonModel) {
            $lesson = null;
            if ($lessonModel->typeIsQuiz()) {
                $lesson = $this->lessonBuilder->createQuizLesson($lessonModel->uuid, $lessonModel->name, $lessonModel->order, $lessonModel->id);
                $quizBlockModel = $lessonModel->lessonBlocksQuiz[0];
                $blockId = (new SlideWrapper($quizBlockModel->slide->getSlideOrLinkData()))->getQuizBlockId();
                $quizName = $quizBlockModel->quiz->title;
                $this->lessonBuilder->addQuiz($lesson, $quizBlockModel->slide_id, $quizBlockModel->order, $quizBlockModel->quiz_id, $quizName, $blockId);
            }
            else {
                $lesson = $this->lessonBuilder->createBlocksLesson($lessonModel->uuid, $lessonModel->name, $lessonModel->order, $lessonModel->id);
                foreach ($lessonModel->lessonBlocks as $blockModel) {
                    $this->lessonBuilder->addSlide($lesson, $blockModel->slide_id, $blockModel->slide->getSlideOrLinkData(), $blockModel->order);
                }
            }
            $this->lessonCollection->addLesson($lesson);
        }
        return $this->lessonCollection;
    }
}
