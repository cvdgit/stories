<?php

declare(strict_types=1);

namespace backend\components\training\local;

use backend\Testing\Questions\Column\ColumnQuestionParams;
use common\models\StoryTestQuestion;
use yii\helpers\Json;

class ColumnQuestion extends Question
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
        $values = [
            'stars' => [
                'total' => $this->starsTotal,
                'current' => $this->makeStars($this->stars, $this),
            ],
            'view' => 'column_question',
            'payload' => Json::decode($this->question->regions),
        ];
        $values = array_merge(
            ColumnQuestionParams::fromArray($this->question->getQuestionParams())->asArray(),
            $values
        );
        return array_merge($values, parent::serialize());
    }
}
