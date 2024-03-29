<?php

namespace backend\components\training\collection;

use backend\components\SlideModifier;
use backend\components\training\base\BaseQuestion;
use backend\components\training\collection\build\Base;
use backend\components\training\collection\build\PassTest;
use backend\components\training\collection\build\Region;
use backend\components\training\collection\build\Sequence;
use common\models\StoryTest;
use common\models\StoryTestQuestion;

class QuizCollection extends BaseCollection
{

    private $testModel;

    public function __construct($data, $stars, StoryTest $testModel)
    {
        $this->testModel = $testModel;
        parent::__construct($data, $stars);
    }

    public function createQuestion($questionData, $stars)
    {
        /** @var StoryTestQuestion $questionData */
        if ($questionData->typeIsRegion()) {
            $question = (new Region($questionData, $stars))->build();
        } elseif ($questionData->typeIsSequence()) {
            $question = (new Sequence($questionData, $stars))->build();
        } elseif ($questionData->typeIsPassTest()) {
            $question = (new PassTest($questionData, $stars))->build();
        } else {
            $question = (new Base($questionData, $stars, $this->testModel))->build();
        }

        /** @var BaseQuestion $question */
        if (count($questionData->storySlides) > 0) {
            $question->setHaveSlides(true);
            $slides = [];
            foreach ($questionData->storySlides as $slide) {
                $data = (new SlideModifier($slide->story_id, $slide->getSlideOrLinkData()))
                    ->addImageUrl()
                    ->addVideoUrl()
                    ->forLesson();
                $slides[] = [
                    'id' => $slide->id,
                    'items' => $data['blocks'],
                ];
            }
            $question->setSlides($slides);
        }

        return $question;
    }
}
