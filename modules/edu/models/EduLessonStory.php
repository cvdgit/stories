<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "edu_lesson_story".
 *
 * @property int $lesson_id
 * @property int $story_id
 * @property int $order
 *
 * @property EduLesson $lesson
 * @property EduStory $story
 */
class EduLessonStory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_lesson_story';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_id', 'story_id'], 'required'],
            [['lesson_id', 'story_id', 'order'], 'integer'],
            [['lesson_id', 'story_id'], 'unique', 'targetAttribute' => ['lesson_id', 'story_id']],
            [['lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduLesson::class, 'targetAttribute' => ['lesson_id' => 'id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduStory::class, 'targetAttribute' => ['story_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lesson_id' => 'Lesson ID',
            'story_id' => 'Story ID',
            'order' => 'Order',
        ];
    }

    public function getLesson(): ActiveQuery
    {
        return $this->hasOne(EduLesson::class, ['id' => 'lesson_id']);
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(EduStory::class, ['id' => 'story_id']);
    }
}
