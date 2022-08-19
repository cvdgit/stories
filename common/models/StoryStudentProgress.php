<?php

namespace common\models;

use Yii;
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
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'story_student_progress';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['story_id', 'student_id', 'updated_at'], 'required'],
            [['story_id', 'student_id', 'progress', 'updated_at'], 'integer'],
            [['story_id', 'student_id'], 'unique', 'targetAttribute' => ['story_id', 'student_id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'story_id' => 'Story ID',
            'student_id' => 'Student ID',
            'progress' => 'Progress',
            'updated_at' => 'Updated At',
        ];
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    public function getStudent(): ActiveQuery
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }
}
