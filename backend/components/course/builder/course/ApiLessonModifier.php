<?php

namespace backend\components\course\builder\course;

use api\modules\v1\models\StorySlide;
use backend\components\course\builder\ApiCourseBuilder;
use backend\components\course\builder\LessonCollectionInterface;
use backend\components\course\LessonBlockForm;
use backend\components\course\LessonForm;
use backend\components\course\LessonQuizForm;
use backend\components\SlideModifier;
use common\models\slide\SlideKind;
use common\models\Story;
use common\models\StoryTest;
use common\services\QuizService;

class ApiLessonModifier
{

    private $builder;
    private $collection;
    private $quizService;

    public function __construct(LessonCollectionInterface $collection, ApiCourseBuilder $builder, QuizService $quizService)
    {
        $this->collection = $collection;
        $this->builder = $builder;
        $this->quizService = $quizService;
    }

    private function getQuizData(int $quizId): array
    {
        if (($quizModel = StoryTest::findOne($quizId)) === null) {
            throw new \DomainException('Quiz not found');
        }
        return $this->quizService->getQuizData($quizModel);
    }

    public function build(): ApiResult
    {
        $lessons = [];
        $slideLinks = [];
        $dividerIndex = 1;
        foreach ($this->collection->getLessons() as $lessonModel) {
            /** @var LessonForm $lessonModel */
            $lesson = null;
            if ($lessonModel->typeIsQuiz()) {
                $lesson = $this->builder->createQuizLesson($lessonModel->id, $lessonModel->name, 'descr');
                $quizBlockModel = $lessonModel->blocks[0];
                /** @var LessonQuizForm $blockModel */
                $this->builder->addQuizBlock($lesson, $quizBlockModel->quiz_id, $this->getQuizData($quizBlockModel->quiz_id));
            }
            else {
                $lesson = $this->builder->createBlocksLesson($lessonModel->id, $lessonModel->name);
                foreach ($lessonModel->blocks as $blockModel) {
                    /** @var LessonBlockForm $blockModel */

                    $data = (new SlideModifier($blockModel->slide_id, $blockModel->data))
                        ->addImageUrl()
                        ->addVideoUrl()
                        ->forLesson();

                    $slideItems = $data['blocks'];
                    $slideLinks = array_merge($slideLinks, $data['links']);

                    if (count($slideItems) > 0) {
                        foreach ($slideItems as $item) {
                            $this->builder->addBlock($lesson, $item);
                        }
                        $this->builder->addDivider($lesson, $dividerIndex++);
                    }
                }
            }
            $lessons[] = $lesson;
        }

        $lessons = $this->unsetLastDivider($lessons);
        $slideLinks = $this->processSlideLinks($slideLinks);

        return new ApiResult($lessons, $slideLinks);
    }

    private function unsetLastDivider(array $lessons): array
    {
        foreach ($lessons as $key => $value) {
            $lastItem = end($value['items']);
            if ($lastItem['type'] === 'divider') {
                array_pop($lessons[$key]['items']);
            }
        }
        return $lessons;
    }

    private function processSlideLinks(array $slideLinks): array
    {
        foreach ($slideLinks as $i => $slideLink) {
            $alias = $slideLink['alias'];
            $number = $slideLink['number'];
            if (($storyModel = Story::findOne(['alias' => $alias])) !== null) {
                if (($slideModel = StorySlide::findSlideByNumber($storyModel->id, $number)) !== null) {
                    if (SlideKind::isQuiz($slideModel)) {
                        unset($slideLinks[$i]['alias'], $slideLinks[$i]['number']);
                        $slideLinks[$i]['items'] = [];
                    }
                    else {
                        $slideData = StorySlide::getSlideData($slideModel);
                        $data = (new SlideModifier($storyModel->id, $slideData))
                            ->addImageUrl()
                            ->addVideoUrl()
                            ->forLesson();
                        $slideItems = $data['blocks'];
                        unset($slideLinks[$i]['alias'], $slideLinks[$i]['number']);
                        $slideLinks[$i]['items'] = $slideItems;
                    }
                }
            }
        }
        return $slideLinks;
    }
}
