<?php

declare(strict_types=1);

namespace backend\widgets;

use backend\Testing\Questions\QuestionRoutes;
use common\models\StoryTest;

class QuestionManageWidget extends BaseQuizManageWidget
{
    /** @var StoryTest */
    public $quizModel;

    public $isCreate = false;

    public function init(): void
    {
        $this->createItemTitle = 'Новый вопрос';
        parent::init();
    }

    public function run(): string
    {
        //if (!$this->quizModel instanceof StoryTest) {
        //    throw new DomainException('Unknown quiz');
        //}

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
            'url' => ['test/update-question', 'question_id' => $item->id, '#' => 'question' . $item->id],
            'active' => $item->id === $this->currentModelId,
            'linkOptions' => ['data-anchor' => 'question' . $item->id],
        ];
    }

    public function getItemsData(): array
    {
        return $this->quizModel->storyTestQuestions;
    }

    private function getCreateQuestionItems(): array
    {
        $quizId = $this->quizModel->id;
        return array_map(static function(string $label, array $route): array {
            return ['label' => $label, 'route' => $route];
        }, array_keys(QuestionRoutes::getRoutes($quizId)), array_values(QuestionRoutes::getRoutes($quizId)));
    }
}
