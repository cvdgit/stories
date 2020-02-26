<?php

namespace common\models;

use DomainException;
use Yii;

/**
 * This is the model class for table "story_test_answer".
 *
 * @property int $id
 * @property int $story_question_id
 * @property string $name
 * @property int $order
 * @property int $is_correct
 * @property string $image
 *
 * @property StoryTestQuestion $storyQuestion
 */
class StoryTestAnswer extends \yii\db\ActiveRecord
{

    const CORRECT_ANSWER = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_question_id', 'name'], 'required'],
            [['story_question_id', 'order', 'is_correct'], 'integer'],
            [['name', 'image'], 'string', 'max' => 255],
            [['story_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTestQuestion::class, 'targetAttribute' => ['story_question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_question_id' => 'Вопрос',
            'name' => 'Ответ',
            'order' => 'Order',
            'is_correct' => 'Ответ правильный',
            'image' => 'Изображение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryQuestion()
    {
        return $this->hasOne(StoryTestQuestion::class, ['id' => 'story_question_id']);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Ответ не найден');
    }

    public function answerIsCorrect()
    {
        return (int)$this->is_correct === self::CORRECT_ANSWER;
    }

    public static function create(int $questionID, string $name, int $isCorrect, int $order = null, string $image = null): StoryTestAnswer
    {
        $model = new self;
        $model->story_question_id = $questionID;
        $model->name = $name;
        $model->is_correct = $isCorrect;
        if ($order !== null) {
            $model->order = $order;
        }
        if ($image !== null) {
            $model->image = $image;
        }
        return $model;
    }

}
