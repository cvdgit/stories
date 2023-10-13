<?php

namespace backend\components\training\local;

use common\models\StoryTestQuestion;
use yii\helpers\Json;

class PassTestQuestion extends Question
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
            'view' => 'pass-test',
            'payload' => Json::decode($this->question->regions),
            'item_view' => $this->question->sort_view === 0 || $this->question->sort_view === 1 ? 'all' : 'one',
            'max_prev_items' => $this->question->max_prev_items
        ], parent::serialize());
    }
}
