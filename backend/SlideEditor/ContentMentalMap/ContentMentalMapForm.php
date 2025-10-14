<?php

declare(strict_types=1);

namespace backend\SlideEditor\ContentMentalMap;

use yii\base\Model;

class ContentMentalMapForm extends Model
{
    public $slideId;
    public $blockId;
    public $mentalMaps;
    public $text;

    public function rules(): array
    {
        return [
            [['slideId', 'blockId', 'mentalMaps', 'text'], 'required'],
            ['slideId', 'integer'],
            [['text', 'blockId'], 'string'],
            ['mentalMaps', 'safe'],
        ];
    }
}
