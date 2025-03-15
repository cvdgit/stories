<?php

declare(strict_types=1);

namespace backend\SlideEditor\UpdateRetelling;

use yii\base\Model;

class UpdateRetellingForm extends Model
{
    public $required;
    public $with_questions;
    public $questions;

    public function rules(): array
    {
        return [
            [['required', 'with_questions'], 'integer'],
            ['questions', 'string'],
        ];
    }
}
