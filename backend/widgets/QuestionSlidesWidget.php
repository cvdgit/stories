<?php

declare(strict_types=1);

namespace backend\widgets;

use common\models\StoryTestQuestion;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class QuestionSlidesWidget extends Widget
{
    /* @var StoryTestQuestion */
    public $model;

    /* @var int */
    public $modelId;

    /**
     * @throws InvalidConfigException
     */
    public function run(): string
    {
        if ($this->model === null && $this->modelId === null) {
            throw new InvalidConfigException('model or modelId is required');
        }

        if ($this->model !== null) {
            $slides = $this->model->getStorySlidesForList();
        } else {
            $question = StoryTestQuestion::findOne($this->modelId);
            if ($question === null) {
                throw new \DomainException('Question not found');
            }
            $slides = $question->getStorySlidesForList();
        }

        return $this->render('question-slides', [
            'slides' => $slides,
            'questionID' => $this->modelId ?? $this->model->id,
        ]);
    }
}
