<?php

namespace backend\components\course;

use common\models\StoryTest;

class LessonQuizForm extends AbstractLessonBlock
{

    public $quiz_id;
    public $block_id;
    public $quiz_name;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['quiz_id', 'integer'],
            [['block_id', 'quiz_name'], 'string'],
        ]);
    }

    public static function create(int $slideId, string $data, int $order, int $quizId, string $quizName, string $blockId = null): self
    {
        $model = new self;
        $model->slide_id = $slideId;
        $model->data = $data;
        $model->order = $order;
        $model->quiz_id = $quizId;
        $model->block_id = $blockId;
        $model->quiz_name = $quizName;
        return $model;
    }
}
