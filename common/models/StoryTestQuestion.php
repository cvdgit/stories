<?php

namespace common\models;

use DomainException;

/**
 * This is the model class for table "story_test_question".
 *
 * @property int $id
 * @property int $story_test_id
 * @property string $name
 * @property int $order
 * @property int $type
 *
 * @property StoryTestAnswer[] $storyTestAnswers
 * @property StoryTest $storyTest
 */
class StoryTestQuestion extends \yii\db\ActiveRecord
{

    const QUESTION_TYPE_RADIO = 0;
    const QUESTION_TYPE_CHECKBOX = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_test_id', 'name', 'type'], 'required'],
            [['story_test_id', 'order', 'type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['story_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['story_test_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_test_id' => 'Тест',
            'name' => 'Вопрос',
            'order' => 'Порядок сортировки',
            'type' => 'Тип',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTestAnswers()
    {
        return $this->hasMany(StoryTestAnswer::class, ['story_question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'story_test_id']);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Вопрос не найден');
    }

    public static function questionTypeArray()
    {
        return [
            self::QUESTION_TYPE_RADIO => 'Один ответ',
            self::QUESTION_TYPE_CHECKBOX => 'Множественный выбор',
        ];
    }

}
