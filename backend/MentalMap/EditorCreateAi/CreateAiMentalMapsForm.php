<?php

declare(strict_types=1);

namespace backend\MentalMap\EditorCreateAi;

use yii\base\Model;

class CreateAiMentalMapsForm extends Model
{
    public $currentSlideId;
    public $mentalMaps;
    public $text;

    public function rules(): array
    {
        return [
            [['currentSlideId', 'mentalMaps', 'text'], 'required'],
            ['currentSlideId', 'integer'],
            ['text', 'string'],
            ['mentalMaps', 'safe'],
        ];
    }
}
