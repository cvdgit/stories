<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math\Update;

use backend\Testing\Questions\Math\MathPayload;
use backend\Testing\Questions\Math\MathQuestionForm;
use common\models\StoryTestQuestion;
use yii\helpers\Json;

class MathQuestionUpdateForm extends MathQuestionForm
{
    public function __construct(StoryTestQuestion $question, $config = [])
    {
        parent::__construct($config);
        $this->name = $question->name;
        $payload = MathPayload::fromPayload(Json::decode($question->regions));
        $this->job = $payload->getJob();
        $this->answers = $payload->getAnswers();
        $this->inputAnswer = $payload->isInputAnswer();
    }
}
