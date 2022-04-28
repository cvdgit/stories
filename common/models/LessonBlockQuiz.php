<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lesson_block_quiz".
 *
 * @property int $lesson_id
 * @property int $slide_id
 * @property int $quiz_id
 * @property int $order
 *
 * @property Lesson $lesson
 * @property StoryTest $quiz
 * @property StorySlide $slide
 */
class LessonBlockQuiz extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_block_quiz';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_id', 'slide_id', 'quiz_id'], 'required'],
            [['lesson_id', 'slide_id', 'quiz_id', 'order'], 'integer'],
            [['lesson_id', 'slide_id', 'quiz_id'], 'unique', 'targetAttribute' => ['lesson_id', 'slide_id', 'quiz_id']],
            [['lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lesson::class, 'targetAttribute' => ['lesson_id' => 'id']],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['quiz_id' => 'id']],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
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
            'quiz_id' => 'Quiz ID',
            'order' => 'Order',
        ];
    }

    /**
     * Gets query for [[Lesson]].
     *
     * @return ActiveQuery
     */
    public function getLesson()
    {
        return $this->hasOne(Lesson::class, ['id' => 'lesson_id']);
    }

    /**
     * Gets query for [[Quiz]].
     *
     * @return ActiveQuery
     */
    public function getQuiz()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'quiz_id']);
    }

    /**
     * Gets query for [[Slide]].
     *
     * @return ActiveQuery
     */
    public function getSlide(): ActiveQuery
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    public static function create(int $lessonId, int $slideId, int $quizId, int $order = 1): self
    {
        $model = new self();
        $model->lesson_id = $lessonId;
        $model->slide_id = $slideId;
        $model->quiz_id = $quizId;
        $model->order = $order;
        return $model;
    }

    public function updateQuizId(int $quizId): void
    {
        $this->quiz_id = $quizId;
        if (!$this->save(false, ['quiz_id'])) {
            throw new \DomainException('LessonBlockQuiz::updateQuizId exception');
        }
    }
}
