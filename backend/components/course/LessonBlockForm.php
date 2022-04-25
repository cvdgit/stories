<?php

namespace backend\components\course;

class LessonBlockForm extends AbstractLessonBlock
{

    public static function create(int $slideId, string $data, int $order): self
    {
        $model = new self;
        $model->slide_id = $slideId;
        $model->data = $data;
        $model->order = $order;
        return $model;
    }
}
