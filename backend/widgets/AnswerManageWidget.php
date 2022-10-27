<?php

namespace backend\widgets;

use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use DomainException;

class AnswerManageWidget extends BaseQuizManageWidget
{

    /** @var StoryTestQuestion */
    public $questionModel;

    public function init()
    {
        $this->createItemTitle = 'Новый ответ';
        parent::init();
    }

    public function run(): string
    {
        if (!$this->questionModel instanceof StoryTestQuestion) {
            throw new DomainException('Unknown quiz');
        }

        return $this->render('answer-manage', [
            'items' => $this->makeNavItems(),
            'renderData' => $this->renderData,
            'createRoute' => $this->createAnswerRoute(),
        ]);
    }

    public function getItemsData(): array
    {
        return $this->questionModel->storyTestAnswersWithHidden;
    }

    public function itemCallback($item): array {
        return $this->createItem($item->name, ['/answer/update', 'id' => $item->id], $item->id === $this->currentModelId);
    }

    private function createAnswerRoute(): array
    {
        return StoryTestAnswer::getCreateAnswerRoute($this->questionModel->id);
    }
}
