<?php

namespace modules\edu\models;

use Yii;

/**
 * This is the model class for table "edu_lesson_story".
 *
 * @property int $lesson_id
 * @property int $story_id
 * @property int $order
 *
 * @property EduLesson $lesson
 * @property Story $story
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
            [['lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduLesson::className(), 'targetAttribute' => ['lesson_id' => 'id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::className(), 'targetAttribute' => ['story_id' => 'id']],
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

    /**
     * Gets query for [[Lesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLesson()
    {
        return $this->hasOne(EduLesson::className(), ['id' => 'lesson_id']);
    }

    /**
     * Gets query for [[Story]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::className(), ['id' => 'story_id']);
    }
}
