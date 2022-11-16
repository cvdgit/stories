<?php

declare(strict_types=1);

namespace modules\testing\widgets;

use modules\testing\models\Question;
use modules\testing\models\Testing;
use yii\base\Widget;

class QuestionViewWidget extends Widget
{
    /** @var Question[] */
    public $questionItems;

    /** @var callable() */
    public $questionItemCallback;

    public $isCreate = false;

    /** @var string */
    public $renderData;

    /** @var int|null */
    public $currentQuestionId;

    public function run(): string
    {
        //if (!$this->quizModel instanceof StoryTest) {
        //    throw new DomainException('Unknown quiz');
        //}

        return $this->render('question-view', [
            'items' => array_map($this->questionItemCallback, $this->questionItems),
            'renderData' => $this->renderData,
            'createItems' => [],
            'isCreate' => $this->isCreate,
        ]);
    }
}
