<?php

namespace backend\models\study_task;

use common\models\Story;
use common\models\StorySlide;
use common\models\StoryStoryTest;
use common\models\StudyTask;

class CreateStudyTaskForm extends BaseStudyTaskForm
{

    public function createTask(int $userID): void
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateStudyTaskForm not valid');
        }

        $this->transactionManager->wrap(function() use ($userID) {

            $storyModel = Story::createStudy($this->title, 'story' . time(), $userID);
            if (!$storyModel->save()) {
                throw new \Exception('Can\'t be saved Story model. Errors: '. implode(', ', $storyModel->getFirstErrors()));
            }

            foreach ($this->slide_ids as $slideID) {
                $slideModel = StorySlide::createSlideLink($storyModel->id, $slideID);
                if (!$slideModel->save()) {
                    throw new \Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $slideModel->getFirstErrors()));
                }
                $testIds = StoryStoryTest::findTestIdsBySlideId($slideID);
                if (count($testIds) > 0) {
                    foreach ($testIds as $testId) {
                        $storyTestModel = StoryStoryTest::create($storyModel->id, $testId);
                        $storyTestModel->save();
                    }
                }
            }

            $taskModel = StudyTask::create($this->title, $storyModel->id, $this->status, $this->description);
            $taskModel->save();
        });
    }
}
