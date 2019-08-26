<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "story_test_result".
 *
 * @property int $id
 * @property int $question_id
 * @property int $user_id
 * @property int $story_id
 * @property int $answer_is_correct
 * @property int $created_at
 *
 * @property StoryTestQuestion $question
 * @property Story $story
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

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'user_id', 'story_id', 'answer_is_correct'], 'required'],
            [['question_id', 'user_id', 'story_id', 'answer_is_correct', 'created_at'], 'integer'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTestQuestion::class, 'targetAttribute' => ['question_id' => 'id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
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
            'question_id' => 'Question ID',
            'user_id' => 'User ID',
            'story_id' => 'Story ID',
            'answer_is_correct' => 'Answer Is Correct',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(StoryTestQuestion::class, ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
