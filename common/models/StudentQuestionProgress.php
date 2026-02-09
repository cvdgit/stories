<?php

namespace common\models;

use frontend\events\StudentTestingFinish;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_question_progress".
 *
 * @property int $student_id
 * @property int $question_id
 * @property int $progress
 * @property int $test_id
 *
 * @property UserStudent $student
 */
class StudentQuestionProgress extends ActiveRecord
{
    private $events = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_question_progress';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'student_id' => 'Student ID',
            'question_id' => 'Question ID',
            'progress' => 'Progress',
            'test_id' => 'Test ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    public static function findProgressModel(int $studentID, int $testID)
    {
        return self::findOne(['student_id' => $studentID, 'test_id' => $testID]);
    }

    public static function findProgressModelsByTest(int $testID)
    {
        return self::findAll(['test_id' => $testID]);
    }

    public static function create(int $studentID, int $questionID, int $progress, int $testID): StudentQuestionProgress
    {
        $model = new self();
        $model->student_id = $studentID;
        $model->question_id = $questionID;
        $model->progress = $progress;
        $model->test_id = $testID;
        return $model;
    }

    public function updateProgress(int $progress): void
    {
        $this->progress = $progress;
        if ($progress === 100) {
            $this->recordEvent(new StudentTestingFinish($this->test_id, $this->student_id));
        }
    }

    public static function resetProgress(int $studentID, int $testID)
    {
        $model = self::findProgressModel($studentID, $testID);
        $model->updateProgress(0);
        $model->save();
    }

    public static function resetProgressByTest(int $testID)
    {
        $models = self::findProgressModelsByTest($testID);
        foreach ($models as $model) {
            $model->updateProgress(0);
            $model->save();
        }
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    private function recordEvent($event): void
    {
        $this->events[] = $event;
    }

    public static function findProgress(int $testId, int $studentId): int
    {
        $model = self::findProgressModel($studentId, $testId);
        if ($model === null) {
            return 0;
        }
        return $model->progress;
    }
}
