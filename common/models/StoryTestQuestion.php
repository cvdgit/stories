<?php

namespace common\models;

use DomainException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "story_test_question".
 *
 * @property int $id
 * @property int $story_test_id
 * @property string $name
 * @property int $order
 * @property int $type
 * @property int $mix_answers
 *
 * @property StoryTestAnswer[] $storyTestAnswers
 * @property StoryTest $storyTest
 */
class StoryTestQuestion extends ActiveRecord
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
            [['story_test_id', 'order', 'type', 'mix_answers'], 'integer'],
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
            'mix_answers' => 'Перемешивать ответы',
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

    public static function questionArray(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public function correctAnswersArray()
    {
        $correctAnswers = array_filter($this->storyTestAnswers, function(StoryTestAnswer $item) {
            return $item->answerIsCorrect();
        });
        return array_values(array_map(function(StoryTestAnswer $item) {
            return $item->id;
        }, $correctAnswers));
    }

}
