<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column\Update;

use backend\Testing\Questions\Column\ColumnQuestionForm;
use common\models\StoryTestQuestion;
use yii\helpers\Json;

class ColumnQuestionUpdateForm extends ColumnQuestionForm
{
    public function __construct(StoryTestQuestion $question, $config = [])
    {
        parent::__construct($config);
        $this->name = $question->name;
        $payload = Json::decode($question->regions);
        $this->firstDigit = $payload['firstDigit'];
        $this->secondDigit = $payload['secondDigit'];
        $this->sign = $payload['sign'];
        $this->result = $payload['result'];
    }
}
