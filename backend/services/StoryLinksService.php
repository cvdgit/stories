<?php

namespace backend\services;

use common\models\StoryStoryTest;

class StoryLinksService
{

    public function createTestLink(int $storyID, int $testID)
    {
        $model = StoryStoryTest::create($storyID, $testID);
        if (!$model->validate()) {
            throw new \DomainException('Model not valid');
        }
        return $model->save(false);
    }

}