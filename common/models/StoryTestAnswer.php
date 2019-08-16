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

}
