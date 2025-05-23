<?php

declare(strict_types=1);

namespace frontend\services;

use common\components\MentalMapThreshold;
use common\models\StorySlide;
use common\models\StoryStudentProgress;
use common\models\UserStudent;
use DomainException;
use frontend\components\ModelDomainException;
use frontend\MentalMap\history\MentalMapHistoryFetcher;
use frontend\MentalMap\history\MentalMapTreeHistoryFetcher;
use frontend\MentalMap\MentalMap;
use frontend\models\StoryStudentStatForm;
use frontend\Retelling\Retelling;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\SlideRetelling;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class StoryStatService
{
    /** @var UserStudent */
    private $student;

    /**
     * @param StoryStudentStatForm $form
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function saveStudentStat(StoryStudentStatForm $form): array
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $this->student = UserStudent::findOne($form->student_id);
        if ($this->student === null) {
            throw new DomainException('Student not found');
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

        $slideContent = (new StoryTestsFetcher())->fetch((int) $form->story_id);

        return $this->calcStoryStudentPercent(
            (int) $form->story_id,
            (int) $form->student_id,
            $slideContent->find(SlideMentalMap::class),
            $slideContent->find(SlideRetelling::class),
        );
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
        return (int) $count;
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
            ->count('DISTINCT id');
        return (int) $count;
    }

    private function getStoryTestingNumber(int $storyId): int
    {
        $count = (new Query())
            ->from('story_story_test')
            ->where(['story_id' => $storyId])
            ->count('DISTINCT test_id');
        return (int) $count;
    }

    private function getFinishedTestingNumber(int $storyId, int $studentId): int
    {
        $count = (new Query())
            ->from('story_story_test')
            ->innerJoin('student_question_progress', 'story_story_test.test_id = student_question_progress.test_id')
            ->where(['story_story_test.story_id' => $storyId, 'student_question_progress.student_id' => $studentId])
            ->andWhere('student_question_progress.progress = 100')
            ->count('DISTINCT story_story_test.test_id');
        return (int) $count;
    }

    /**
     * @param array<SlideMentalMap> $mentalMapItems
     * @return int
     */
    private function getFinishedMentalMapsNumber(array $mentalMapItems): int
    {
        $doneNumber = 0;
        foreach ($mentalMapItems as $item) {
            $mentalMap = MentalMap::findOne($item->getMentalMapId());
            if ($mentalMap !== null) {
                $threshold = MentalMapThreshold::getThreshold(Yii::$app->params, $mentalMap->payload);
                if ($mentalMap->isMentalMapAsTree()) {
                    $history = (new MentalMapTreeHistoryFetcher())->fetch($mentalMap->uuid, $this->student->user_id, $mentalMap->getTreeData(), $threshold);
                } else {
                    $history = (new MentalMapHistoryFetcher())->fetch($mentalMap->getImages(), $mentalMap->uuid, $this->student->user_id, $threshold);
                }
                if (MentalMap::isDone($history, $threshold)) {
                    $doneNumber++;
                }
            }
        }
        return $doneNumber;
    }

    /**
     * @param int $storyId
     * @param array<SlideRetelling> $retellingItems
     * @return int
     */
    private function getFinishedRetellingNumber(int $storyId, array $retellingItems): int
    {
        $doneNumber = 0;
        foreach ($retellingItems as $item) {
            $retelling = Retelling::findOne($item->getRetellingId());
            if ($retelling !== null) {
                $completed = (new Query())
                    ->select([
                        'overallSimilarity' => new Expression('MAX(rh.overall_similarity)'),
                    ])
                    ->from(['rh' => 'retelling_history'])
                    ->where([
                        'story_id' => $storyId,
                        'slide_id' => $item->getSlideId(),
                        'user_id' => $this->student->user_id,
                    ])
                    ->andWhere('rh.overall_similarity >= 90')
                    ->scalar();
                if ($completed !== null) {
                    $doneNumber++;
                }
            }
        }
        return $doneNumber;
    }

    public function calcStoryStudentPercent(
        int $storyId,
        int $studentId,
        array $mentalMapItems,
        array $retellingItems
    ): array
    {
        $viewedSlidesNumber = 0;
        $viewedStorySlidesNumber = $this->getViewedSlidesNumber($storyId, $studentId);
        $viewedSlidesNumber += $viewedStorySlidesNumber;

        $finishedTestingNumber = $this->getFinishedTestingNumber($storyId, $studentId);
        $viewedSlidesNumber += $finishedTestingNumber;

        $finishedMentalMapNumber = $this->getFinishedMentalMapsNumber($mentalMapItems);
        $viewedSlidesNumber += $finishedMentalMapNumber;

        $viewedSlidesNumber += $this->getFinishedRetellingNumber($storyId, $retellingItems);


        $numberOfSlides = 0;
        $numberOfStorySlides = $this->getStorySlidesNumber($storyId);
        $numberOfSlides += $numberOfStorySlides;

        $numberOfTests = $this->getStoryTestingNumber($storyId);
        $numberOfSlides += $numberOfTests;

        $numberOfMentalMaps = count($mentalMapItems);
        $numberOfSlides += $numberOfMentalMaps;

        $numberOfRetelling = count($retellingItems);
        $numberOfSlides += $numberOfRetelling;

        if (($storyProgress = StoryStudentProgress::findOne(['story_id' => $storyId, 'student_id' => $studentId],
            )) === null) {
            $storyProgress = StoryStudentProgress::create($storyId, $studentId);
        }

        $progress = $storyProgress->calcProgress($numberOfSlides, $viewedSlidesNumber);
        $storyProgress->updateProgress($progress);

        if (!$storyProgress->save()) {
            throw ModelDomainException::create($storyProgress);
        }

        return [
            'progress' => $progress,
            'story' => [
                'slide' => $numberOfStorySlides,
                'test' => $numberOfTests,
                'mental_map' => $numberOfMentalMaps,
                'retelling' => $numberOfRetelling,
                'total' => $numberOfSlides,
            ],
            'finished' => [
                'slide' => $viewedStorySlidesNumber,
                'test' => $finishedTestingNumber,
                'mental_map' => $finishedMentalMapNumber,
                'total' => $viewedSlidesNumber,
            ]
        ];
    }
}
