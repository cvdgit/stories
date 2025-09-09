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
    public $text_target_percentage;
    public $repetition_mode;
    public $threshold;
    public $all_important_words_included;
    public $payload;

    public function rules(): array
    {
        return [
            [['mental_map_id', 'image_fragment_id', 'content', 'overall_similarity'], 'required'],
            [['mental_map_id', 'image_fragment_id'], 'string', 'max' => 36],
            [
                [
                    'story_id',
                    'overall_similarity',
                    'text_hiding_percentage',
                    'text_target_percentage',
                    'slide_id',
                    'threshold',
                ],
                'integer',
            ],
            [['repetition_mode', 'all_important_words_included'], 'boolean'],
            [['content', 'payload'], 'safe'],
        ];
    }
}
