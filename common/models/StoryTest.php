<?php

namespace common\models;

use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "story_test".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $mix_answers
 * @property int $remote
 * @property int $question_list_id
 * @property string $question_list_name
 * @property string $description_text
 * @property string $header
 *
 * @property StoryTestQuestion[] $storyTestQuestions
 */
class StoryTest extends ActiveRecord
{

    const REMOTE = 1;

    public $question_list = [];
    public $question_number;

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
            [['title', 'header'], 'required'],
            [['status', 'mix_answers', 'remote', 'question_list_id'], 'integer'],
            [['title', 'question_list_name', 'header'], 'string', 'max' => 255],
            [['description_text'], 'string'],
            [['question_list'], 'safe'],
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
            'description_text' => 'Описание',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'mix_answers' => 'Перемешивать ответы',
            'remote' => 'Вопросы из neo4j',
            'question_list' => 'Список вопросов',
            'header' => 'Заголовок',
            'question_number' => 'Количество вопросов',
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
        return ArrayHelper::map(self::find()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    public static function getRemoteTestArray(): array
    {
        return ArrayHelper::map(self::find()->where('remote = :remote', [':remote' => self::REMOTE])->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Тест не найден');
    }

    public function isRemote()
    {
        return (int) $this->remote === self::REMOTE;
    }

    public function haveQuestions()
    {
        return count($this->storyTestQuestions) > 0;
    }

}
