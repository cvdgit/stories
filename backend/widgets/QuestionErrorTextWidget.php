<?php

namespace backend\widgets;

use common\models\StoryTestQuestion;
use DomainException;
use yii\base\Widget;

class QuestionErrorTextWidget extends Widget
{

    /** @var StoryTestQuestion */
    public $questionModel;

    public function run(): string
    {
        if (!$this->questionModel instanceof StoryTestQuestion) {
            throw new DomainException('Question model required');
        }

        if (($errorText = $this->questionModel->getAnswersErrorText()) === '') {
            return '';
        }

        return $this->render('question-error', [
            'errorText' => $errorText,
        ]);
    }
}
