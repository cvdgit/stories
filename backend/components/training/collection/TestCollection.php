<?php

declare(strict_types=1);

namespace backend\components\training\collection;

use backend\components\training\base\BaseQuestion;
use backend\components\training\collection\build\Base;
use backend\components\training\collection\build\DragWords;
use backend\components\training\collection\build\Grouping;
use backend\components\training\collection\build\ImageGaps;
use backend\components\training\collection\build\PassTest;
use backend\components\training\collection\build\Poetry;
use backend\components\training\collection\build\Region;
use backend\components\training\collection\build\Sequence;

use common\models\StoryTest;
use common\models\StoryTestQuestion;

class TestCollection extends BaseCollection
{
    private $testModel;

    public function __construct($data, $stars, StoryTest $testModel)
    {
        $this->testModel = $testModel;
        parent::__construct($data, $stars);
    }

    public function createQuestion(StoryTestQuestion $questionData, array $stars): BaseQuestion
    {
        $type = $questionData->getQuestionType();
        if ($questionData->typeIsRegion()) {
            $builder = new Region($questionData, $stars);
        } elseif ($questionData->typeIsSequence()) {
            $builder = new Sequence($questionData, $stars);
        } elseif ($questionData->typeIsPassTest()) {
            $builder = new PassTest($questionData, $stars);
        } elseif ($questionData->typeIsDragWords()) {
            $builder = new DragWords($questionData, $stars);
        } elseif ($questionData->typeIsPoetry()) {
            $builder = new Poetry($questionData, $stars);
        } elseif ($questionData->typeIsImageGaps()) {
            $builder = new ImageGaps($questionData, $stars);
        } elseif ($type->isGrouping()) {
            $builder = new Grouping($questionData, $stars);
        } else {
            $builder = new Base($questionData, $stars, $this->testModel);
        }

        $question = $builder->build();

        if (count($questionData->storySlides) > 0) {
            $question->setHaveSlides(true);
        }

        return $question;
    }
}

