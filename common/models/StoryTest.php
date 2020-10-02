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
 * @property int $parent_id
 * @property string $question_params
 * @property string $incorrect_answer_text
 * @property int $source
 * @property int $word_list_id
 * @property int $answer_type
 *
 * @property StoryTestQuestion[] $storyTestQuestions
 */
class StoryTest extends ActiveRecord
{

    public const LOCAL = 0;
    public const REMOTE = 1;

    public const TEST = 1;
    public const NEO = 2;
    public const LIST = 3;

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
            [['status', 'mix_answers', 'remote', 'question_list_id', 'parent_id', 'source', 'word_list_id', 'answer_type'], 'integer'],
            [['title', 'question_list_name', 'header', 'question_params', 'incorrect_answer_text'], 'string', 'max' => 255],
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
            'parent_id' => 'Родительский тест',
            'question_params' => 'Параметры вопроса',
            'incorrect_answer_text' => 'Текст неправильного ответа',
            'source' => 'Источник вопросов',
            'word_list_id' => 'Список слов',
            'answer_type' => 'Тип ответов',
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
        return ArrayHelper::map(self::find()->where('source = :source', [':source' => self::NEO])->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    public static function getLocalTestArray(): array
    {
        return ArrayHelper::map(self::find()->where('source = :source', [':source' => self::LIST])->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
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
        return (int) $this->source === self::NEO;
    }

    public function haveQuestions()
    {
        return count($this->storyTestQuestions) > 0;
    }

    public static function findAllAsArray(int $id)
    {
        return self::find()
            ->where('id = :id', [':id' => $id])
            ->andWhere('remote = 0')
            ->with('storyTestQuestions.storyTestAnswers')
            ->asArray()
            ->all();
    }

    public function getChildrenTestsAsArray()
    {
        return self::find()
            ->where('parent_id = :id', [':id' => $this->id])
            ->asArray()
            ->all();
    }

    public static function create(string $title, string $header, string $description, string $incorrectAnswerText, int $remote = self::LOCAL)
    {
        $model = new self();
        $model->title = $title;
        $model->header = $header;
        $model->description_text = $description;
        $model->incorrect_answer_text = $incorrectAnswerText;
        $model->remote = $remote;
        return $model;
    }

    public static function createVariant(int $parentID,
                                         string $title,
                                         string $header,
                                         string $description,
                                         string $incorrectAnswerText,
                                         int $questionID,
                                         string $questionName,
                                         string $questionParams)
    {
        $model = self::create($title, $header, $description, $incorrectAnswerText, self::REMOTE);
        $model->parent_id = $parentID;
        $model->question_list_id = $questionID;
        $model->question_list_name = $questionName;
        $model->question_params = $questionParams;
        return $model;
    }

    public function getParent()
    {
        if ((int) $this->parent_id === 0) {
            return null;
        }
        return self::findOne($this->parent_id);
    }

    public static function testSourcesAsArray()
    {
        return [
            self::TEST => 'Тест',
            self::NEO => 'Neo4j',
            self::LIST => 'Список слов',
        ];
    }

    public function isSourceTest()
    {
        return (int) $this->source === self::TEST;
    }

    public function isSourceWordList()
    {
        return (int) $this->source === self::LIST;
    }

    public static function sourceText(int $source)
    {
        $values = self::testSourcesAsArray();
        return $values[$source];
    }

    public const ANSWER_TYPE_DEFAULT = 0;
    public const ANSWER_TYPE_NUMPAD = 1;

    public static function answerTypeAsArray()
    {
        return [
            self::ANSWER_TYPE_DEFAULT => 'По умолчанию',
            self::ANSWER_TYPE_NUMPAD => 'Цифровая клавиатура',
        ];
    }

    public function isAnswerTypeNumPad()
    {
        return (int) $this->answer_type === self::ANSWER_TYPE_NUMPAD;
    }

}
