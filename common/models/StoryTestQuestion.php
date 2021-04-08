<?php

namespace common\models;

use backend\models\question\QuestionType;
use DomainException;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "story_test_question".
 *
 * @property int $id
 * @property int $story_test_id
 * @property string $name
 * @property int $order
 * @property int $type
 * @property int $mix_answers
 * @property string $image
 * @property string $regions;
 *
 * @property StoryTestAnswer[] $storyTestAnswers
 * @property StoryTest $storyTest
 */
class StoryTestQuestion extends ActiveRecord
{

    const QUESTION_TYPE_RADIO = 0;
    const QUESTION_TYPE_CHECKBOX = 1;

    public $answer_number;
    public $correct_answer_number;

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
            [['name', 'image'], 'string', 'max' => 255],
            [['story_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['story_test_id' => 'id']],
            [['regions'], 'safe'],
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
            'answer_number' => 'Количество ответов',
            'correct_answer_number' => 'Количество верных ответов',
            'image' => 'Изображение',
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

    public static function questionArray(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public function getCorrectAnswers()
    {
        return array_filter($this->storyTestAnswers, function(StoryTestAnswer $item) {
            return $item->answerIsCorrect();
        });
    }

    public function correctAnswersArray()
    {
        return array_values(array_map(function(StoryTestAnswer $item) {
            return $item->id;
        }, $this->getCorrectAnswers()));
    }

    public static function create(int $testID, string $name, int $type, int $order = 1, int $mixAnswers = 0, string $image = '', string $regions = '')
    {
        $model = new self();
        $model->story_test_id = $testID;
        $model->name = $name;
        $model->type = $type;
        $model->order = $order;
        $model->mix_answers = $mixAnswers;
        $model->image = $image;
        $model->regions = $regions;
        return $model;
    }

    public static function createRegion(int $testID, string $name, string $regions = '', int $order = 1, int $mixAnswers = 0, string $image = '')
    {
        return self::create($testID, $name, QuestionType::REGION, $order, $mixAnswers, $image, $regions);
    }

    public function getImagesPath()
    {
        return Yii::getAlias('@public') . Yii::$app->params['test.question.images'] . '/' . $this->story_test_id . '/';
    }

    public function getImageUrl(): string
    {
        if (empty($this->image)) {
            return '';
        }
        return Yii::$app->params['test.question.images'] . '/' . $this->story_test_id . '/' . $this->image;
    }

    public function typeIsRegion(): bool
    {
        return (int) $this->type === QuestionType::REGION;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->typeIsRegion()) {
            $path = $this->getImagesPath() . $this->image;
            FileHelper::unlink($path);
        }
    }

    public function deleteImage(): void
    {
        $path = $this->getImagesPath() . $this->image;
        FileHelper::unlink($path);
    }

}
