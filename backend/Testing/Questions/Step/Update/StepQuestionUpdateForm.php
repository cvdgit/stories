<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Step\Update;

use backend\Testing\Questions\Step\StepQuestionForm;
use common\models\StoryTestQuestion;
use yii\helpers\Json;

class StepQuestionUpdateForm extends StepQuestionForm
{
    public function __construct(StoryTestQuestion $question, $config = [])
    {
        parent::__construct($config);
        $this->name = $question->name;
        $payload = Json::decode($question->regions);
        $this->steps = $payload['steps'];
        $this->job = $payload['job'];
    }
}
