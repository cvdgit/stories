<?php

namespace backend\components\training\local;

use common\models\StoryTestQuestion;
use yii\helpers\Json;

class RegionQuestion extends Question
{

    private $starsTotal = 5;
    private $stars;
    private $question;

    public function __construct(StoryTestQuestion $question, array $stars)
    {
        parent::__construct($question->story_test_id,
            $question->id,
            $question->name,
            true,
            $question->mix_answers,
            $question->type);
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
            'view' => 'region',
            'params' => [
                'regions' => Json::decode($this->question->regions),
                'image' => $this->question->getImageUrl(),
            ],
        ], parent::serialize());
    }
}