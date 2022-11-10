<?php

declare(strict_types=1);

namespace frontend\services;

use common\models\StorySlide;
use common\models\StoryStudentProgress;
use frontend\components\ModelDomainException;
use frontend\models\StoryStudentStatForm;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class StoryStatService
{
    /**
     * @throws Exception
     */
    public function saveStudentStat(StoryStudentStatForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $command = Yii::$app->db->createCommand();
        $command->insert('story_student_stat', [
            'story_id' => $form->story_id,
            'student_id' => $form->student_id,
            'slide_id' => $form->slide_id,
            'session' => $form->session,
            'created_at' => time(),
        ]);
        $command->execute();

        $this->calcStoryStudentPercent((int)$form->story_id, (int)$form->student_id);
    }

    private function getViewedSlidesNumber(int $storyId, int $studentId): int
    {
        $count = (new Query())
            ->from('story_student_stat')
            ->innerJoin('story_slide', 'story_student_stat.slide_id = story_slide.id')
            ->where([
                'story_student_stat.story_id' => $storyId,
                'story_student_stat.student_id' => $studentId,
                'story_slide.status' => StorySlide::STATUS_VISIBLE,
            ])
            ->andWhere(['in', 'story_slide.kind', [StorySlide::KIND_SLIDE, StorySlide::KIND_LINK]])
            ->count('DISTINCT story_student_stat.slide_id');
        return (int)$count;
    }

    private function getStorySlidesNumber(int $storyId): int
    {
        $count = (new Query())
            ->from('{{%story_slide}}')
            ->where([
                'story_id' => $storyId,
                'status' => StorySlide::STATUS_VISIBLE,
            ])
            ->andWhere(['in', 'kind', [StorySlide::KIND_SLIDE, StorySlide::KIND_LINK]])
            ->count('id');
        return (int)$count;
    }

    private function getStoryTestingNumber(int $storyId): int
    {
        $count = (new Query())
            ->from('story_story_test')
            ->where(['story_id' => $storyId])
            ->count('DISTINCT test_id');
        return (int)$count;
    }

    private function getFinishedTestingNumber(int $storyId, int $studentId): int
    {
        $count = (new Query())
            ->from('story_story_test')
            ->innerJoin('student_question_progress', 'story_story_test.test_id = student_question_progress.test_id')
            ->where(['story_story_test.story_id' => $storyId, 'student_question_progress.student_id' => $studentId])
            ->andWhere('student_question_progress.progress = 100')
            ->count('DISTINCT story_story_test.test_id');
        return (int)$count;
    }

    public function calcStoryStudentPercent(int $storyId, int $studentId): void
    {
        $viewedSlidesNumber = $this->getViewedSlidesNumber($storyId, $studentId);
        $finishedTestingNumber = $this->getFinishedTestingNumber($storyId, $studentId);
        $viewedSlidesNumber += $finishedTestingNumber;

        $numberOfSlides = $this->getStorySlidesNumber($storyId);
        $numberOfTesting = $this->getStoryTestingNumber($storyId);
        $numberOfSlides += $numberOfTesting;
        $numberOfSlides--;

        if (($storyProgress = StoryStudentProgress::findOne(['story_id' => $storyId, 'student_id' => $studentId])) === null) {
            $storyProgress = StoryStudentProgress::create($storyId, $studentId);
        }

        $progress = $storyProgress->calcProgress($numberOfSlides, $viewedSlidesNumber);
        $storyProgress->updateProgress($progress);

        if (!$storyProgress->save()) {
            throw ModelDomainException::create($storyProgress);
        }
    }
}
