<?php

namespace common\models;

use backend\helpers\SelectSlideWidgetHelper;
use backend\models\question\QuestionType;
use backend\models\question\region\RegionImage;
use backend\models\question\sequence\SortView;
use DomainException;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
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
 * @property string $regions
 * @property string $hint
 * @property string $audio_file_id
 * @property int $sort_view
 *
 * @property StoryTestAnswer[] $storyTestAnswers
 * @property StoryTestAnswer[] $storyTestAnswersWithHidden
 * @property StoryTest $storyTest
 * @property AudioFile $audioFile
 */
class StoryTestQuestion extends ActiveRecord
{

    const QUESTION_TYPE_RADIO = 0;
    const QUESTION_TYPE_CHECKBOX = 1;

    public $answer_number;
    public $correct_answer_number;

    /** @var RegionImage */
    private $regionImage;

    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'storyTestAnswers',
                ],
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function tableName()
    {
        return 'story_test_question';
    }

    public function rules()
    {
        return [
            [['story_test_id', 'name', 'type'], 'required'],
            [['story_test_id', 'order', 'type', 'mix_answers', 'audio_file_id'], 'integer'],
            [['image', 'hint'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 512],
            [['story_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['story_test_id' => 'id']],
            [['regions'], 'safe'],
            ['sort_view', 'in', 'range' => SortView::values()],
        ];
    }

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

    public function getStoryTestAnswers(): ActiveQuery
    {
        return $this->hasMany(StoryTestAnswer::class, ['story_question_id' => 'id'])
            ->where(['hidden' => 0])
            ->orderBy(['order' => SORT_ASC]);
    }

    public function getStoryTestAnswersWithHidden(): ActiveQuery
    {
        return $this->hasMany(StoryTestAnswer::class, ['story_question_id' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }

    public function getStoryTest(): ActiveQuery
    {
        return $this->hasOne(StoryTest::class, ['id' => 'story_test_id']);
    }

    public function getStoryTestQuestionStorySlides(): ActiveQuery
    {
        return $this->hasMany(StoryTestQuestionStorySlide::class, ['story_test_question_id' => 'id']);
    }

    public function getStorySlides(): ActiveQuery
    {
        return $this->hasMany(StorySlide::class, ['id' => 'story_slide_id'])
            ->viaTable('story_test_question_story_slide', ['story_test_question_id' => 'id'])
            ->innerJoin('story_test_question_story_slide', 'story_test_question_story_slide.story_slide_id = story_slide.id')
            ->andWhere('story_test_question_story_slide.story_test_question_id = :question', [':question' => $this->id])
            ->orderBy(['story_test_question_story_slide.sort_order' => SORT_ASC]);
    }

    public function getStoryTestResults(): ActiveQuery
    {
        return $this->hasMany(StoryTestResult::class, ['question_id' => 'id']);
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

    public static function create(int $testID, string $name, int $type, int $order = null, int $mixAnswers = 0, string $image = '', string $regions = ''): self
    {
        $model = new self();
        $model->story_test_id = $testID;
        $model->name = $name;
        $model->type = $type;
        if ($order === null) {
            $order = (int)(new Query())
                ->from(self::tableName())
                ->where('story_test_id = :test', [':test' => $testID])
                ->count();
            $order++;
        }
        $model->order = $order;
        $model->mix_answers = $mixAnswers;
        $model->image = $image;
        $model->regions = $regions;
        return $model;
    }

    public static function createRegion(int $testID, string $name, string $regions = '', int $order = null, int $mixAnswers = 0, string $image = '')
    {
        return self::create($testID, $name, QuestionType::REGION, $order, $mixAnswers, $image, $regions);
    }

    public static function createSequence(int $testID, string $name, int $order = null, int $view = SortView::VERTICAL): self
    {
        $model = self::create($testID, $name, QuestionType::SEQUENCE, $order, 1);
        $model->sort_view = $view;
        return $model;
    }

    public function getImagesPath(): string
    {
        return $this->getRegionImage()->getImagesPath();
    }

    public function getImageUrl(): string
    {
        return $this->getRegionImage()->getImageUrl();
    }

    public function getOrigImageUrl(): string
    {
        return $this->getRegionImage()->getOrigImageUrl();
    }

    public function getImagePath(): string
    {
        return $this->getRegionImage()->getImagePath();
    }

    public function getOrigImagePath(): string
    {
        return $this->getRegionImage()->getOrigImagePath();
    }

    public function typeIsRegion(): bool
    {
        return $this->type === QuestionType::REGION;
    }

    public function typeIsSequence(): bool
    {
        return (new QuestionType($this->type))->isSequence();
    }

    public function deleteRegionImages(): void
    {
        if (!empty($this->image)) {
            $name = pathinfo($this->image, PATHINFO_FILENAME);
            if (!empty($name)) {
                $imagesPath = $this->getRegionImage()->getImagesPath();
                array_map(static function($path) use ($imagesPath) {
                    if (file_exists($path) && rtrim($imagesPath, DIRECTORY_SEPARATOR) === dirname($path)) {
                        FileHelper::unlink($path);
                    }
                }, glob($imagesPath . $name . '*.*'));
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->typeIsRegion()) {
            if (!empty($this->image)) {
                $this->deleteRegionImages();
            }
        }
    }

    public function deleteImage(): void
    {
        $images = [
            $this->getImagePath(),
            $this->getOrigImagePath(),
        ];
        foreach ($images as $imagePath) {
            if (!empty($imagePath) && file_exists($imagePath)) {
                FileHelper::unlink($imagePath);
            }
        }
    }

    public function getRegionImage(): RegionImage
    {
        if ($this->regionImage === null) {
            $this->regionImage = new RegionImage($this->owner);
        }
        return $this->regionImage;
    }

    public function getQuestionType(): QuestionType
    {
        return new QuestionType($this->type);
    }

    public function getUpdateRoute(): array
    {
        $route = ['test/update-question', 'question_id' => $this->id];
        /*if ($this->typeIsRegion()) {
            $route = ['question/update', 'id' => $this->id];
        }
        if ($this->typeIsSequence()) {
            $route = ['test/question-sequence/update', 'id' => $this->id];
        }*/
        return $route;
    }

    public function checkAnswersData(): bool
    {
        $answers = $this->storyTestAnswers;
        if (count($answers) === 0) {
            throw new DomainException('В вопросе нет ответов');
        }
        $correctAnswers = $this->getCorrectAnswers();
        if (count($correctAnswers) === 0) {
            throw new DomainException('В вопросе нет правильных ответов');
        }

        $questionType = $this->getQuestionType();
        if ($questionType->isSingle() && count($correctAnswers) > 1) {
            throw new DomainException('В вопросе должен быть только один правильный ответ');
        }
        if ($questionType->isMultiple() && count($correctAnswers) === 1) {
            throw new DomainException('Тип вопроса должен быть "Один ответ"');
        }
        return true;
    }

    public function isCorrectData(): bool
    {
        try {
            $this->checkAnswersData();
            return true;
        }
        catch (\Exception $ex) {

        }
        return false;
    }

    public function getAnswersErrorText(): string
    {
        try {
            $this->checkAnswersData();
        }
        catch (\Exception $ex) {
            return $ex->getMessage();
        }
        return '';
    }

    public static function updateQuestionsOrder(int $testID, array $orders): void
    {
        if (count($orders) === 0) {
            return;
        }
        $command = Yii::$app->db->createCommand();
        $order = 1;
        foreach ($orders as $questionID) {
            $command->update(self::tableName(), ['order' => $order], 'id = :id AND story_test_id = :test', [':id' => $questionID, ':test' => $testID]);
            $command->execute();
            $order++;
        }
    }

    public function getModifiedSlides(): array
    {
        return Story::modifySlides($this->getStorySlides()->with('story')->all());
    }

    public function getStorySlidesForList(): array
    {
        return SelectSlideWidgetHelper::getSlides($this->getStorySlides()->with('story')->all());
    }

    public function getAudioFile(): ActiveQuery
    {
        return $this->hasOne(AudioFile::class, ['id' => 'audio_file_id']);
    }

    public function getAudioFilesPath(bool $url = false): string
    {
        return AudioFile::getAudioFilesPath($this->story_test_id, $url);
    }

    public function getAudioFileUrl(): ?string
    {
        $audioFile = $this->audioFile;
        if (!$audioFile) {
            return null;
        }
        return $audioFile->getAudioFileUrl();
    }

    public static function getCreateQuestionRoute(int $quizId): array
    {
        return ['test/create-question', 'test_id' => $quizId];
    }

    public static function getCreateRegionQuestionRoute(int $quizId): array
    {
        return ['question/create', 'test_id' => $quizId, 'type' => QuestionType::REGION];
    }

    public static function getCreateSequenceQuestionRoute(int $quizId): array
    {
        return ['test/question-sequence/create', 'test_id' => $quizId];
    }
}
