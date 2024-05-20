<?php

declare(strict_types=1);

namespace frontend\Retelling;

use yii\base\Model;

class RetellingHistoryForm extends Model
{
    public $story_id;
    public $slide_id;
    public $content;
    public $overall_similarity;

    public function rules(): array
    {
        return [
            [['story_id', 'content', 'overall_similarity', 'slide_id'], 'required'],
            [['story_id', 'overall_similarity', 'slide_id'], 'integer'],
            ['content', 'safe'],
        ];
    }
}
