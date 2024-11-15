<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Gpt\Update;

use backend\Testing\Questions\Gpt\GptQuestionForm;
use common\models\StoryTestQuestion;
use yii\helpers\Json;

class GptQuestionUpdateForm extends GptQuestionForm
{
    public function __construct(StoryTestQuestion $question, $config = [])
    {
        parent::__construct($config);
        $this->name = $question->name;

        $payload = Json::decode($question->regions);
        $this->job = $payload['job'];
        $this->promptId = $payload['promptId'];
    }
}
