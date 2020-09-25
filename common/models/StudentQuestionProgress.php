<?php

namespace common\models;

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

    public static function create(int $studentID, int $questionID, int $progress, int $testID)
    {
        $model = new self();
        $model->student_id = $studentID;
        $model->question_id = $questionID;
        $model->progress = $progress;
        $model->test_id = $testID;
        return $model;
    }

    public function updateProgress(int $progress)
    {
        $this->progress = $progress;
    }

    public static function resetProgress(int $studentID, int $testID)
    {
        $model = self::findProgressModel($studentID, $testID);
        $model->updateProgress(0);
        $model->save();
    }

}
