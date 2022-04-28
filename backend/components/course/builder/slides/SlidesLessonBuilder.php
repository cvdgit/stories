<?php

namespace backend\components\course\builder\slides;

use backend\components\course\builder\BaseBuilder;
use backend\components\course\builder\LessonCollectionInterface;
use backend\components\SlideWrapper;
use common\models\slide\SlideKind;
use common\models\StoryTest;
use Ramsey\Uuid\Uuid;

class SlidesLessonBuilder extends BaseBuilder
{

    private function findQuiz(int $id): StoryTest
    {
        if (($quiz = StoryTest::findOne($id)) === null) {
            throw new \DomainException('Quiz not found');
        }
        return $quiz;
    }

    public function build(array $models): LessonCollectionInterface
    {
        $currentLesson = null;
        $lessonIndex = 1;
        $blockIndex = 1;
        foreach ($models as $slide) {

            $slideData = $slide->getSlideOrLinkData();

            $end = next($models) === false;
            if (($currentLesson !== null && SlideKind::isQuiz($slide)) || $end) {
                if ($currentLesson) {
                    $this->lessonCollection->addLesson($currentLesson);
                }
                if (!$end) {
                    $currentLesson = null;
                    $lessonIndex++;
                    $blockIndex = 1;
                }
            }

            if (SlideKind::isQuiz($slide)) {

                $currentLesson = $this->lessonBuilder->createQuizLesson(Uuid::uuid4(), 'Тест', $lessonIndex);

                $slideWrapper = new SlideWrapper($slideData);
                $quizId = $slideWrapper->findTestId();
                $quizName = $this->findQuiz($quizId)->title;
                $this->lessonBuilder->addQuiz($currentLesson, $slide->id, 1, $quizId, $quizName, $slideWrapper->getQuizBlockId());

                $this->lessonCollection->addLesson($currentLesson);

                $currentLesson = null;
                $blockIndex = 1;
            }
            else {
                if ($currentLesson === null) {
                    $currentLesson = $this->lessonBuilder->createBlocksLesson(Uuid::uuid4(), "Раздел $lessonIndex", $lessonIndex);
                }
                $this->lessonBuilder->addSlide($currentLesson, $slide->id, $slideData, $blockIndex);
                $blockIndex++;
            }
        }
        return $this->lessonCollection;
    }
}
