<?php


namespace backend\services;


use common\models\Story;

class StoryService
{

    public function create(string $title, int $userID, array $categories)
    {
        $model = Story::create($title, $userID, $categories);
        return $model;
    }

}