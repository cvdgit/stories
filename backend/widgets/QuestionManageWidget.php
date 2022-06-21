<?php

namespace backend\widgets;

use common\models\StoryTest;
use common\models\StoryTestQuestion;
use DomainException;

class QuestionManageWidget extends BaseQuizManageWidget
{

    /** @var StoryTest */
    public $quizModel;

    public $isCreate = false;

    public function init()
    {
        $this->createItemTitle = 'Новый вопрос';
        parent::init();
    }

    public function run(): string
    {
        if (!$this->quizModel instanceof StoryTest) {
            throw new DomainException('Unknown quiz');
        }

        return $this->render('question-manage', [
            'items' => $this->makeNavItems(),
            'renderData' => $this->renderData,
            'createItems' => $this->getCreateQuestionItems(),
            'isCreate' => $this->isCreate,
        ]);
    }

    public function itemCallback($item): array
    {
        return [
            'label' => $item->name,
            'url' => ['test/update-question', 'question_id' => $item->id],
            'active' => $item->id === $this->currentModelId,
        ];
    }

    public function getItemsData(): array
    {
        return $this->quizModel->storyTestQuestions;
    }

    private function getCreateQuestionItems(): array
    {
        $quizId = $this->quizModel->id;
        return [
            ['label' => 'По умолчанию', 'route' => StoryTestQuestion::getCreateQuestionRoute($quizId)],
            ['label' => 'Выбор области', 'route' => StoryTestQuestion::getCreateRegionQuestionRoute($quizId)],
            ['label' => 'Последовательность', 'route' => StoryTestQuestion::getCreateSequenceQuestionRoute($quizId)],
        ];
    }
}
