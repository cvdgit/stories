<?php

declare(strict_types=1);

namespace backend\services;

use common\models\StorySlide;

class StorySlideService
{
    public function create(int $storyID, string $data, int $kind): StorySlide
    {
        $model = StorySlide::createSlide($storyID);
        $model->data = $data;
        $model->kind = $kind;
        return $model;
    }
}
