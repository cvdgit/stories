<?php

namespace common\models;

use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "story_test".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $mix_answers
 *
 * @property StoryTestQuestion[] $storyTestQuestions
 */
class StoryTest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['status', 'created_at', 'updated_at', 'mix_answers'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название теста',
            'description' => 'Описание',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'mix_answers' => 'Перемешивать ответы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTestQuestions()
    {
        return $this->hasMany(StoryTestQuestion::class, ['story_test_id' => 'id']);
    }

    public static function getTestArray(): array
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'title');
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Тест не найден');
    }

}
