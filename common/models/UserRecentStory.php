<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_recent_story".
 *
 * @property int $user_id
 * @property int $story_id
 * @property int $updated_at
 *
 * @property Story $story
 * @property User $user
 */
class UserRecentStory extends ActiveRecord
{

    public static function tableName()
    {
        return 'user_recent_story';
    }

    public function rules()
    {
        return [
            [['user_id', 'story_id', 'updated_at'], 'required'],
            [['user_id', 'story_id', 'updated_at'], 'integer'],
            [['user_id', 'story_id'], 'unique', 'targetAttribute' => ['user_id', 'story_id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'story_id' => 'Story ID',
            'updated_at' => 'Updated At',
        ];
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

    public static function createRecent(int $userId, int $storyId): self
    {
        $model = self::find()
            ->where(['user_id' => $userId, 'story_id' => $storyId])
            ->one();
        if ($model === null) {
            $model = new self();
            $model->user_id = $userId;
            $model->story_id = $storyId;
        }
        $model->updated_at = time();
        return $model;
    }

    public static function getUserRecentStories(int $userId): array
    {
        $models = self::find()
            ->where('user_id = :user', [':user' => $userId])
            ->with('story')
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(10)
            ->all();
        return array_map(static function($model) {
            return $model->story;
        }, $models);
    }
}
