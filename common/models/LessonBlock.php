<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "lesson_block".
 *
 * @property int $lesson_id
 * @property int $slide_id
 * @property int $order
 *
 * @property Lesson $lesson
 * @property StorySlide $slide
 */
class LessonBlock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_block';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_id', 'slide_id'], 'required'],
            [['lesson_id', 'slide_id', 'order'], 'integer'],
            [['lesson_id', 'slide_id'], 'unique', 'targetAttribute' => ['lesson_id', 'slide_id']],
            [['lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lesson::className(), 'targetAttribute' => ['lesson_id' => 'id']],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::className(), 'targetAttribute' => ['slide_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lesson_id' => 'Lesson ID',
            'slide_id' => 'Slide ID',
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
        return $this->hasOne(Lesson::className(), ['id' => 'lesson_id']);
    }

    /**
     * Gets query for [[Slide]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::className(), ['id' => 'slide_id']);
    }

    public function updateOrder(int $order): void
    {
        if ($this->order !== $order) {
            $this->order = $order;
        }
    }

    private static function getMaxOrder(int $lessonId): int
    {
        $order = (new Query())
            ->from(self::tableName())
            ->where('lesson_id = :lesson', [':lesson' => $lessonId])
            ->max('`order`');
        return $order ?? 0;
    }

    public static function create(int $lessonId, int $slideId, int $order = null): self
    {
        $model = new self();
        $model->lesson_id = $lessonId;
        $model->slide_id = $slideId;
        if ($order === null) {
            $order = self::getMaxOrder($lessonId) + 1;
        }
        $model->order = $order;
        return $model;
    }
}
