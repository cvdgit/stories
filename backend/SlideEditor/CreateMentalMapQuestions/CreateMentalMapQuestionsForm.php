<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateMentalMapQuestions;

use yii\base\Model;

class CreateMentalMapQuestionsForm extends Model
{
    public $mentalMapId;
    public $fragments;
    public $required;

    public function rules(): array
    {
        return [
            [['mentalMapId', 'fragments', 'required'], 'required'],
            ['mentalMapId', 'string', 'max' => 36],
            ['fragments', 'safe'],
            ['required', 'boolean'],
        ];
    }
}
