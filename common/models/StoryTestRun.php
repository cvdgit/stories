<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_test_run".
 *
 * @property int $id
 * @property int $test_id
 * @property int $student_id
 * @property int $created_at
 *
 * @property UserStudent $student
 * @property StoryTest $test
 */
class StoryTestRun extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test_run';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'test_id']);
    }

    public static function create(int $testID, int $studentID)
    {
        $model = new self();
        $model->test_id = $testID;
        $model->student_id = $studentID;
        return $model;
    }

}
