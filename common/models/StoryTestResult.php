<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "story_test_result".
 *
 * @property int $id
 * @property int $story_test_id
 * @property int $user_id
 * @property int $correct_answer
 *
 * @property StoryTest $storyTest
 * @property User $user
 */
class StoryTestResult extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_test_id', 'user_id', 'correct_answer'], 'required'],
            [['story_test_id', 'user_id', 'correct_answer'], 'integer'],
            [['story_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['story_test_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_test_id' => 'Story Test ID',
            'user_id' => 'User ID',
            'correct_answer' => 'Correct Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'story_test_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
