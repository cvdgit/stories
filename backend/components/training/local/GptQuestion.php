<?php

declare(strict_types=1);

namespace backend\components\training\local;

use common\models\StoryTestQuestion;
use yii\helpers\Json;

class GptQuestion extends Question
{
    private $starsTotal = 5;
    private $stars;
    private $question;

    public function __construct(StoryTestQuestion $question, array $stars)
    {
        parent::__construct($question->id, $question->name,true, $question->mix_answers, $question->type);
        $this->stars = $stars;
        $this->question = $question;
    }

    public function serialize()
    {
        return array_merge([
            'stars' => [
                'total' => $this->starsTotal,
                'current' => $this->makeStars($this->stars, $this),
            ],
            'view' => 'gpt_question',
            'payload' => Json::decode($this->question->regions),
        ], parent::serialize());
    }
}
