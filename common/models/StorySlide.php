<?php

namespace common\models;

use backend\components\SlideWrapper;
use backend\models\NeoSlideRelations;
use common\models\slide\SlideKind;
use DomainException;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "story_slide".
 *
 * @property int $id
 * @property int $story_id
 * @property string $data
 * @property int $number
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $kind
 * @property int $link_slide_id
 *
 * @property NeoSlideRelations[] $neoSlideRelations
 * @property StoryFeedback[] $storyFeedbacks
 * @property Story $story
 * @property StorySlideBlock[] $storySlideBlocks
 * @property StoryStatistics[] $storyStatistics
 * @property LessonBlock[] $lessonBlocks
 * @property Lesson[] $lessons
 */
class StorySlide extends ActiveRecord
{

    const STATUS_VISIBLE = 1;
    const STATUS_HIDDEN = 2;

    const KIND_SLIDE = 0;
    const KIND_LINK = 1;
    const KIND_QUESTION = 2;
    public const KIND_MENTAL_MAP = 3;
    public const KIND_RETELLING = 5;

    public static function tableName(): string
    {
        return '{{%story_slide}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['story_id', 'data', 'number'], 'required'],
            [['story_id', 'number', 'status', 'kind', 'link_slide_id'], 'integer'],
            [['data'], 'string'],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'story_id' => 'Story ID',
            'data' => 'Data',
            'number' => 'Number',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'kind' => 'Kind',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStorySlideBlocks()
    {
        return $this->hasMany(StorySlideBlock::class, ['slide_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNeoSlideRelations()
    {
        return $this->hasMany(NeoSlideRelations::class, ['slide_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStoryFeedbacks()
    {
        return $this->hasMany(StoryFeedback::class, ['slide_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStoryStatistics()
    {
        return $this->hasMany(StoryStatistics::class, ['slide_id' => 'id']);
    }

    public static function getNextSlideNumber(int $storyID): int
    {
        return (new Query())
            ->from(self::tableName())
            ->where('story_id = :story', [':story' => $storyID])
            ->max('number') + 1;
    }

    public static function createSlide(int $storyId, int $number = null): self
    {
        $slide = new self();
        $slide->story_id = $storyId;
        if ($number === null) {
            $slide->number = (new Query())
                    ->from(self::tableName())
                    ->where(['story_id' => $storyId])
                    ->max('number') + 1;
        } else {
            $slide->number = $number;
        }
        return $slide;
    }

    public static function createSlideFull(int $storyID, string $data, int $number = null, int $status = self::STATUS_VISIBLE, int $kind = self::KIND_SLIDE, $linkSlideID = null): self
    {
        $model = new self();
        $model->story_id = $storyID;
        $model->data = $data;
        if ($number === null) {
            $number = self::getNextSlideNumber($storyID);
        }
        $model->number = $number;
        $model->status = $status;
        $model->kind = $kind;
        $model->link_slide_id = $linkSlideID;
        return $model;
    }

    public static function createSlideLink(int $storyID, int $linkSlideID): self
    {
        $slide = new self();
        $slide->story_id = $storyID;
        $slide->kind = self::KIND_LINK;
        $slide->number = (new Query())->from(self::tableName())->where('story_id = :story', [':story' => $storyID])->max('number') + 1;
        $slide->link_slide_id = $linkSlideID;
        $slide->data = 'link';
        return $slide;
    }

    public static function deleteSlide(int $slideID)
    {
        $slide = self::findSlide($slideID);
        return $slide->delete();
    }

    public static function findSlideByID(int $slideID)
    {
        return self::findOne($slideID);
    }

    public static function findSlide(int $id): StorySlide
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Слайд не найден.');
    }

    public static function findSlideByNumber(int $storyID, int $number)
    {
        return self::find()
            ->where('story_id = :story', [':story' => $storyID])
            ->andWhere('number = :number', [':number' => $number])
            ->one();
    }

    public static function findFirstSlide(int $storyID)
    {
        return self::find()
            ->where('story_id = :story', [':story' => $storyID])
            ->orderBy(['number' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function findPrevSlide(): ?StorySlide
    {
        return self::find()
            ->where(['story_id' => $this->story_id])
            ->andWhere(['<', 'number', $this->number])
            ->orderBy(['number' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            Story::updateSlideNumber($this->story_id);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        // Обновить количество слайдов в истории
        Story::updateSlideNumber($this->story_id);

        if ($this->isQuestion()) {
            $slideWrapper = new SlideWrapper($this->data);
            if (($testId = $slideWrapper->findTestId()) !== null) {
                StoryStoryTest::deleteStoryTest($this->story_id, $testId);
            }
        }

        // Изменить номера остальных слайдов
        Story::deleteSlideNumber($this->story_id, $this->number);

        // Если текущий слайд используется как ссылка - обнулить поле в этих слайдах
        if (!$this->isLink()) {
            Story::convertLinksToSlides($this->id);
        }

        parent::afterDelete();
    }

    /**
     * Gets query for [[LessonBlocks]].
     *
     * @return ActiveQuery
     */
    public function getLessonBlocks(): ActiveQuery
    {
        return $this->hasMany(LessonBlock::class, ['slide_id' => 'id']);
    }

    /**
     * Gets query for [[Lessons]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getLessons(): ActiveQuery
    {
        return $this->hasMany(Lesson::class, ['id' => 'lesson_id'])
            ->viaTable('lesson_block', ['slide_id' => 'id']);
    }

    public function isLink(): bool
    {
        return $this->kind === self::KIND_LINK;
    }

    public function isQuestion(): bool
    {
        return $this->kind === self::KIND_QUESTION;
    }

    public function blockArray(): array
    {
        return array_map(static function(StorySlideBlock $block) {
            return [
                'id' => $block->id,
                'type' => 'button',
                'title' => $block->title,
            ];
        }, $this->storySlideBlocks);
    }

    public function isHidden(): bool
    {
        return $this->status === self::STATUS_HIDDEN;
    }

    public function updateData(string $data): void
    {
        $this->data = $data;
        if (!$this->save(false)) {
            throw new DomainException('Story slide update data exception');
        }
    }

    public function toggleVisible(): int
    {
        return ($this->status === self::STATUS_VISIBLE) ? self::STATUS_HIDDEN : self::STATUS_VISIBLE;
    }

    public function updateVisible(int $visible): void
    {
        $this->status = $visible;
        $this->save(false, ['status']);
    }

    public function setQuestionSlide(): void
    {
        $this->kind = self::KIND_QUESTION;
        $this->save(false, ['kind']);
    }

    public function setKindSlide(): void
    {
        $this->kind = self::KIND_SLIDE;
        $this->save(false, ['kind']);
    }

    public static function getSlideData(int $id): string
    {
        $slide = self::findOne($id);
        if ($slide === null) {
            throw new DomainException('Slide not found');
        }
        return $slide->data;
    }

    public static function deleteAllFinalSlides(int $storyID): void
    {
        self::deleteAll('story_id = :story AND kind = :kind_final', [':story' => $storyID, ':kind_final' => SlideKind::FINAL_SLIDE]);
    }

    public function getSlideOrLinkData(): string
    {
        if (SlideKind::isLink($this)) {
            if (($slideLinkId = $this->link_slide_id) === null) {
                throw new \DomainException('Slide link ID is null');
            }
            if (($slideLinkModel = self::findOne($slideLinkId)) === null) {
                throw new \DomainException('Linked slide is null');
            }
            return $slideLinkModel->data;
        }
        return $this->data;
    }

    public static function findStorySlide(int $storyId, int $slideId): ?StorySlide
    {
        return self::find()
            ->where([
                'id' => $slideId,
                'story_id' => $storyId,
            ])
            ->one();
    }

    public function copySlide(StorySlide $sourceSlide, int $newSlideStoryId): StorySlide
    {
        $slide = new self();
        $slide->story_id = $newSlideStoryId;
        $slide->number = self::find()
                ->where(['story_id' => $newSlideStoryId])
                ->max('number') + 1;
        $slide->data = $sourceSlide->getSlideOrLinkData();
        $slide->status = $sourceSlide->status;
        $slide->kind = $sourceSlide->kind;
        $slide->link_slide_id = $sourceSlide->link_slide_id;
        return $slide;
    }

    public static function slideKindText(int $kind): string
    {
        $kinds = [
            self::KIND_SLIDE => 'Слайд',
            self::KIND_LINK => 'Ссылка',
            self::KIND_QUESTION => 'Тест',
            self::KIND_MENTAL_MAP => 'Ментальная карта',
        ];
        return $kinds[$kind] ?? 'Unknown kind';
    }

    public function setHidden(bool $hidden = true): void
    {
        $this->status = $hidden ? self::STATUS_HIDDEN : self::STATUS_VISIBLE;
    }
}
