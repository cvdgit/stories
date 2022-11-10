<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_student_progress".
 *
 * @property int $story_id
 * @property int $student_id
 * @property int $progress
 * @property int $updated_at
 *
 * @property Story $story
 * @property UserStudent $student
 */
class StoryStudentProgress extends ActiveRecord
{
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
            ],
        ];
    }

    public static function tableName(): string
    {
        return 'story_student_progress';
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    public function getStudent(): ActiveQuery
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    public function statusIsDone(): bool
    {
        return $this->progress === 100;
    }

    public function statusInProgress(): bool
    {
        return $this->progress > 0 && !$this->statusIsDone();
    }

    public static function create(int $storyId, int $studentId, int $progress = 0): self
    {
        $model = new self();
        $model->story_id = $storyId;
        $model->student_id = $studentId;
        $model->progress = $progress;
        return $model;
    }

    public function calcProgress(int $numberOfSlides, int $viewedSlidesNumber): int
    {
        $percent = round($viewedSlidesNumber * 100 / $numberOfSlides);
        if ($percent > 100) {
            $percent = 100;
        }
        if ($percent < 0) {
            $percent = 0;
        }
        return $percent;
    }

    public function updateProgress(int $progress): void
    {
        $this->progress = $progress;
    }
}
