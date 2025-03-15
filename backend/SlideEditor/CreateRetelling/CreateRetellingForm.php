<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateRetelling;

use yii\base\Model;

class CreateRetellingForm extends Model
{
    public $story_id;
    public $slide_id;
    public $required;
    public $with_questions;
    public $questions;

    public function rules(): array
    {
        return [
            [['story_id', 'slide_id', 'required', 'with_questions'], 'integer'],
            ['questions', 'string'],
        ];
    }
}
