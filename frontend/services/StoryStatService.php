<?php

declare(strict_types=1);

namespace frontend\services;

use common\models\StorySlide;
use common\services\TransactionManager;
use frontend\components\ModelDomainException;
use frontend\models\StoryStudentStatForm;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class StoryStatService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
    public function saveStudentStat(StoryStudentStatForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $this->transactionManager->wrap(function() use ($form) {

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
        });
    }

    public function calcStoryStudentPercent(int $storyId, int $studentId): void
    {

        $viewedSlidesNumber = (new Query())
            ->from('{{%story_student_stat}}')
            ->where(['story_id' => $storyId])
            ->andWhere(['student_id' => $studentId])
            ->count('DISTINCT slide_id');

        // все видимые слайды
        $numberOfSlides = (new Query())
            ->from('{{%story_slide}}')
            ->where(['story_id' => $storyId])
            ->andWhere(['status' => StorySlide::STATUS_VISIBLE])
            ->count('id');

        $numberOfSlides--; // отнять последний слайд - Конец

        if ($viewedSlidesNumber > 0 && $numberOfSlides > 0) {

            $percent = round($viewedSlidesNumber * 100 / $numberOfSlides);

            if ($percent > 100) {
                $percent = 100;
            }

            if ($percent < 0) {
                $percent = 0;
            }

            $progressExists = (new Query())
                ->from('story_student_progress')
                ->where(['story_id' => $storyId])
                ->andWhere(['student_id' => $studentId])
                ->exists();

            $command = Yii::$app->db->createCommand();
            if ($progressExists) {

                $command->update('{{%story_student_progress}}', ['progress' => $percent, 'updated_at' => time()], ['story_id' => $storyId, 'student_id' => $studentId]);
            }
            else {

                $command->insert('{{%story_student_progress}}', [
                    'story_id' => $storyId,
                    'student_id' => $studentId,
                    'progress' => $percent,
                    'updated_at' => time(),
                ]);
            }

            $command->execute();
        }
    }
}
