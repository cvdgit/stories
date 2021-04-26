<?php

namespace common\models;

use common\helpers\Url;
use common\models\test\AnswerType;
use common\models\test\SourceType;
use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
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
 * @property int $strict_answer
 * @property string $wrong_answers_params
 * @property string $input_voice
 * @property int $shuffle_word_list
 * @property string $recording_lang
 * @property int $remember_answers;
 * @property int $ask_question;
 * @property string $ask_question_lang
 *
 * @property StoryTestQuestion[] $storyTestQuestions
 * @property Story[] $stories
 * @property StoryStoryTest[] $storyStoryTests
 * @property StoryTestRun[] $storyTestRuns
 * @property StoryTest $parentTest;
 * @property TestWordList $wordList;
 */
class StoryTest extends ActiveRecord
{

    public const LOCAL = 0;
    public const REMOTE = 1;

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
            [['status', 'mix_answers', 'remote', 'question_list_id', 'parent_id', 'source', 'word_list_id', 'answer_type', 'strict_answer', 'remember_answers', 'ask_question'], 'integer'],
            [['shuffle_word_list'], 'integer'],
            [['title', 'question_list_name', 'header', 'question_params', 'incorrect_answer_text', 'input_voice', 'recording_lang', 'ask_question_lang'], 'string', 'max' => 255],
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
            'strict_answer' => 'Строгое сравнение ответов',
            'input_voice' => 'Голос синтезатора',
            'recording_lang' => 'Язык распознавания',
            'shuffle_word_list' => 'Перемешивать элементы списка',
            'remember_answers' => 'Запоминать ответы',
            'ask_question' => 'Произносить вопросы',
            'ask_question_lang' => 'Язык синтезатора вопросов',
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
        $subQuery = (new Query())
            ->select('t3.parent_id')
            ->from(['t3' => self::tableName()])
            ->where('t3.source = :source', [':source' => SourceType::NEO])
            ->andWhere('t3.parent_id > 0');
        $query = (new Query())
            ->select(['t.id AS id', "CASE WHEN t2.title IS NULL THEN t.title ELSE CONCAT(t.title, ' (', t2.title, ')') END AS title"])
            ->from(['t' => self::tableName()])
            ->leftJoin(['t2' => self::tableName()], 't2.id = t.parent_id')
            ->where('t.source = :source', [':source' => SourceType::NEO])
            ->andWhere(['not in', 't.id', $subQuery])
            ->orderBy(['IFNULL(t.title, t2.title)' => SORT_ASC]);
        return ArrayHelper::map($query->all(), 'id', 'title');
    }

    public static function getLocalTestArray(): array
    {
        return ArrayHelper::map(self::find()->where(['in', 'source', [SourceType::TEST, SourceType::LIST]])->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
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
        return (int) $this->source === SourceType::NEO;
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
            ->with('stories')
            ->where('parent_id = :id', [':id' => $this->id])
            ->asArray()
            ->all();
    }

    public function getChildrenTestsCount()
    {
        return self::find()
            ->where('parent_id = :id', [':id' => $this->id])
            ->count();
    }

    public static function create(string $title, string $header, string $description, string $incorrectAnswerText, int $remote = self::LOCAL, int $source = SourceType::TEST)
    {
        $model = new self();
        $model->title = $title;
        $model->header = $header;
        $model->description_text = $description;
        $model->incorrect_answer_text = $incorrectAnswerText;
        $model->remote = $remote;
        $model->source = $source;
        return $model;
    }

    public static function createVariant(int $parentID,
                                         string $title,
                                         string $header,
                                         string $description,
                                         string $incorrectAnswerText,
                                         int $questionID,
                                         string $questionName,
                                         string $questionParams,
                                         $wrongAnswersParams = null)
    {
        $model = self::create($title, $header, $description, $incorrectAnswerText, self::REMOTE, SourceType::NEO);
        $model->parent_id = $parentID;
        $model->question_list_id = $questionID;
        $model->question_list_name = $questionName;
        $model->question_params = $questionParams;
        $model->wrong_answers_params = $wrongAnswersParams;
        return $model;
    }

    public function getParent()
    {
        if ((int) $this->parent_id === 0) {
            return null;
        }
        return self::findOne($this->parent_id);
    }

    public function isSourceTest()
    {
        return (int) $this->source === SourceType::TEST;
    }

    public function isSourceWordList()
    {
        return (int) $this->source === SourceType::LIST;
    }

    public function isAnswerTypeNumPad()
    {
        return (int) $this->answer_type === AnswerType::NUMPAD;
    }

    public function isAnswerTypeInput()
    {
        return (int) $this->answer_type === AnswerType::INPUT;
    }

    public function isAnswerTypeRecording()
    {
        return (int) $this->answer_type === AnswerType::RECORDING;
    }

    public function isAnswerTypeMissingWords()
    {
        return (int) $this->answer_type === AnswerType::MISSING_WORDS;
    }

    public function getQuestionData($filter = null): array
    {
        $query = StoryTestQuestion::find()
            ->where('story_test_id = :id', [':id' => $this->id])
            ->with('storyTestAnswers');
        if ($filter !== null) {
            $ids = array_map(static function($item) {
                return $item['entity_id'];
            }, $filter);
            $query->andFilterWhere(['not in', 'id', $ids]);
        }
        return $query->all();
    }

    public function getQuestionDataCount()
    {
        return StoryTestQuestion::find()
            ->where('story_test_id = :id', [':id' => $this->id])
            ->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryStoryTests()
    {
        return $this->hasMany(StoryStoryTest::class, ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['id' => 'story_id'])->viaTable('story_story_test', ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTestRuns()
    {
        return $this->hasMany(StoryTestRun::class, ['test_id' => 'id']);
    }

    public function getParentTest()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    public function isShuffleQuestions()
    {
        return (int) $this->shuffle_word_list === 1;
    }

    public function haveWordList()
    {
        return !empty($this->word_list_id);
    }

    public function isVariant()
    {
        return $this->parent_id > 0;
    }

    public function getRunUrl()
    {
        return Url::toRoute(['/test/view', 'id' => $this->id]);
    }

    public function isRememberAnswers(): bool
    {
        return (int) $this->remember_answers === 1;
    }

    public function getWordList()
    {
        return $this->hasOne(TestWordList::class, ['id' => 'word_list_id']);
    }

    public static function findAllByWordList(int $wordListID)
    {
        return self::find()
            ->where('word_list_id = :id', [':id' => $wordListID])
            ->all();
    }

}
