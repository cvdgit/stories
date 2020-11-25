<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_story_test".
 *
 * @property int $story_id
 * @property int $test_id
 *
 * @property Story $story
 * @property StoryTest $test
 */
class StoryStoryTest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_story_test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'test_id'], 'required'],
            [['story_id', 'test_id'], 'integer'],
            [['story_id', 'test_id'], 'unique', 'targetAttribute' => ['story_id', 'test_id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'story_id' => 'Story ID',
            'test_id' => 'Test ID',
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
    public function getTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'test_id']);
    }

    public static function create(int $storyID, int $testID)
    {
        $model = new self();
        $model->story_id = $storyID;
        $model->test_id = $testID;
        return $model;
    }

}
