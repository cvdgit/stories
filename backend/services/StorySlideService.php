<?php

namespace backend\services;

use common\models\StorySlide;

class StorySlideService
{

    public function create(int $storyID, string $data, int $kind)
    {
        $model = StorySlide::createSlide($storyID);
        $model->data = $data;
        $model->kind = $kind;
        return $model;
    }

}
