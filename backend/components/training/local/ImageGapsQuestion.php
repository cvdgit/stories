<?php

declare(strict_types=1);

namespace backend\components\training\local;

use common\models\StoryTestQuestion;
use yii\helpers\Json;

class ImageGapsQuestion extends Question
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
        $imageUrl = '/test_images/image_gaps/' . $this->question->image;
        [$width, $height] = getimagesize(\Yii::getAlias('@public') . $imageUrl);
        return array_merge([
            'stars' => [
                'total' => $this->starsTotal,
                'current' => $this->makeStars($this->stars, $this),
            ],
            'view' => 'image-gaps',
            'payload' => Json::decode($this->question->regions),
            'params' => [
                'image' => $imageUrl,
                'imageWidth' => $width,
                'imageHeight' => $height,
            ],
            'max_prev_items' => $this->question->max_prev_items
        ], parent::serialize());
    }
}
