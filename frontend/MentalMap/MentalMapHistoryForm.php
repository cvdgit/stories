<?php

declare(strict_types=1);

namespace frontend\MentalMap;

use yii\base\Model;

class MentalMapHistoryForm extends Model
{
    public $story_id;
    public $slide_id;
    public $mental_map_id;
    public $image_fragment_id;
    public $content;
    public $overall_similarity;
    public $text_hiding_percentage;

    public function rules(): array
    {
        return [
            [['story_id', 'mental_map_id', 'image_fragment_id', 'content', 'overall_similarity', 'slide_id'], 'required'],
            [['mental_map_id', 'image_fragment_id'], 'string', 'max' => 36],
            [['story_id', 'overall_similarity', 'text_hiding_percentage', 'slide_id'], 'integer'],
            ['content', 'safe'],
        ];
    }
}
