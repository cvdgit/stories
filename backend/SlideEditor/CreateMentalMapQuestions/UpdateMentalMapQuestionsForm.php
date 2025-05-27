<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateMentalMapQuestions;

use yii\base\Model;

class UpdateMentalMapQuestionsForm extends Model
{
    public $mentalMapId;
    public $blockId;
    public $slideId;
    public $required;
    public $fragments;

    public function rules(): array
    {
        return [
            [['slideId'], 'integer'],
            ['required', 'boolean'],
            [['blockId', 'mentalMapId'], 'string', 'max' => 36],
            ['fragments', 'safe'],
        ];
    }
}
