<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math\Update;

use backend\Testing\Questions\Math\MathQuestionForm;
use common\models\StoryTestQuestion;
use yii\helpers\Json;

class MathQuestionUpdateForm extends MathQuestionForm
{
    public function __construct(StoryTestQuestion $question, $config = [])
    {
        parent::__construct($config);
        $this->name = $question->name;
        $payload = Json::decode($question->regions);
        $this->job = $payload['job'];
        $this->answers = $payload['answers'] ?? [];
        $this->inputAnswer = $payload['isInputAnswer'] ?? false;
        /*if ($this->inputAnswer) {
            $this->inputAnswerId = $this->answers[0]['id'];
        }*/
    }
}
