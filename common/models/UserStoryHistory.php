<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_story_history".
 *
 * @property int $user_id
 * @property int $story_id
 * @property int $updated_at
 * @property int $percent
 *
 * @property Story $story
 * @property User $user
 */
class UserStoryHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_story_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'story_id', 'updated_at'], 'required'],
            [['user_id', 'story_id', 'updated_at', 'percent'], 'integer'],
            [['user_id', 'story_id'], 'unique', 'targetAttribute' => ['user_id', 'story_id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::className(), 'targetAttribute' => ['story_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'story_id' => 'Story ID',
            'updated_at' => 'Updated At',
            'percent' => 'Percent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::className(), ['id' => 'story_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
