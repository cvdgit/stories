<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_test_question_story_slide".
 *
 * @property int $story_test_question_id
 * @property int $story_slide_id
 * @property int $sort_order
 *
 * @property StorySlide $storySlide
 * @property StoryTestQuestion $storyTestQuestion
 */
class StoryTestQuestionStorySlide extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test_question_story_slide';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_test_question_id', 'story_slide_id'], 'required'],
            [['story_test_question_id', 'story_slide_id', 'sort_order'], 'integer'],
            [['story_test_question_id', 'story_slide_id'], 'unique', 'targetAttribute' => ['story_test_question_id', 'story_slide_id']],
            [['story_slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['story_slide_id' => 'id']],
            [['story_test_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTestQuestion::class, 'targetAttribute' => ['story_test_question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'story_test_question_id' => 'Story Test Question ID',
            'story_slide_id' => 'Story Slide ID',
            'sort_order' => 'Sort Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorySlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'story_slide_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTestQuestion()
    {
        return $this->hasOne(StoryTestQuestion::class, ['id' => 'story_test_question_id']);
    }

    public static function deleteByQuestionID(int $questionID): void
    {
        self::deleteAll('story_test_question_id = :question', [':question' => $questionID]);
    }

    public static function create(int $questionID, int $slideID, int $order = 1): self
    {
        $model = new self();
        $model->story_test_question_id = $questionID;
        $model->story_slide_id = $slideID;
        $model->sort_order = $order;
        return $model;
    }
}
