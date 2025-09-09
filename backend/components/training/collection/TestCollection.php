<?php

declare(strict_types=1);

namespace backend\components\training\collection;

use backend\components\training\base\BaseQuestion;
use backend\components\training\collection\build\Base;
use backend\components\training\collection\build\ColumnQuestionBuilder;
use backend\components\training\collection\build\DragWords;
use backend\components\training\collection\build\GptQuestionBuilder;
use backend\components\training\collection\build\Grouping;
use backend\components\training\collection\build\ImageGaps;
use backend\components\training\collection\build\MathQuestionBuilder;
use backend\components\training\collection\build\PassTest;
use backend\components\training\collection\build\Poetry;
use backend\components\training\collection\build\Region;
use backend\components\training\collection\build\Sequence;
use backend\components\training\collection\build\StepQuestionBuilder;
use common\models\StoryTest;

class TestCollection extends BaseCollection
{
    private $testModel;

    public function __construct(array $data, array $stars, StoryTest $testModel)
    {
        $this->testModel = $testModel;
        parent::__construct($data, $stars);
    }

    public function createQuestion($questionData, $stars): BaseQuestion
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
        } elseif ($type->isGptQuestion()) {
            $builder = new GptQuestionBuilder($questionData, $stars);
        } elseif ($type->isMathQuestion()) {
            $builder = new MathQuestionBuilder($questionData, $stars);
        } elseif ($type->isStepQuestion()) {
            $builder = new StepQuestionBuilder($questionData, $stars);
        } elseif ($type->isColumnQuestion()) {
            $builder = new ColumnQuestionBuilder($questionData, $stars);
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

