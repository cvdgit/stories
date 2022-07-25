<?php

namespace frontend\models\feedback;

use yii\base\Model;

class CreateFeedbackForm extends Model
{

    public $slide_id;
    public $text;
    public $testing_id;
    public $question_id;
    public $story_id;

    public function __construct(int $storyId, $config = [])
    {
        $this->story_id = $storyId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['slide_id', 'text'], 'required'],
            [['slide_id', 'testing_id', 'question_id'], 'integer'],
        ];
    }
}
