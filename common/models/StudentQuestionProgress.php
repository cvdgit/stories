<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_question_progress".
 *
 * @property int $student_id
 * @property int $question_id
 * @property int $progress
 *
 * @property UserQuestionHistory $question
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(UserQuestionHistory::class, ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    public static function findProgressModel(int $studentID, int $questionID)
    {
        return self::findOne(['student_id' => $studentID, 'question_id' => $questionID]);
    }

    public static function create(int $studentID, int $questionID, int $progress)
    {
        $model = new self();
        $model->student_id = $studentID;
        $model->question_id = $questionID;
        $model->progress = $progress;
        return $model;
    }

    public function updateProgress(int $progress)
    {
        $this->progress = $progress;
    }

}
