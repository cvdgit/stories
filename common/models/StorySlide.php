<?php

namespace common\models;

use backend\models\NeoSlideRelations;
use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
 */
class StorySlide extends ActiveRecord
{

    const STATUS_VISIBLE = 1;
    const STATUS_HIDDEN = 2;

    const KIND_SLIDE = 0;
    const KIND_LINK = 1;
    const KIND_QUESTION = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_slide';
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
            [['story_id', 'data', 'number'], 'required'],
            [['story_id', 'number', 'status', 'kind', 'link_slide_id'], 'integer'],
            [['data'], 'string'],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorySlideBlocks()
    {
        return $this->hasMany(StorySlideBlock::class, ['slide_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNeoSlideRelations()
    {
        return $this->hasMany(NeoSlideRelations::class, ['slide_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryFeedbacks()
    {
        return $this->hasMany(StoryFeedback::class, ['slide_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryStatistics()
    {
        return $this->hasMany(StoryStatistics::class, ['slide_id' => 'id']);
    }

    public static function createSlide(int $storyID)
    {
        $slide = new self();
        $slide->story_id = $storyID;
        $slide->number = (new Query())->from(self::tableName())->where('story_id = :story', [':story' => $storyID])->max('number') + 1;
        return $slide;
    }

    public static function createSlideFull(int $storyID, string $data, int $number, int $status = self::STATUS_VISIBLE, int $kind = self::KIND_SLIDE, $linkSlideID = null): self
    {
        $model = new self();
        $model->story_id = $storyID;
        $model->data = $data;
        $model->number = $number;
        $model->status = $status;
        $model->kind = $kind;
        $model->link_slide_id = $linkSlideID;
        return $model;
    }

    public static function createSlideLink(int $storyID, int $linkSlideID)
    {
        $slide = new self();
        $slide->story_id = $storyID;
        $slide->kind = self::KIND_LINK;
        $slide->number = (new Query())->from(self::tableName())->where('story_id = :story', [':story' => $storyID])->max('number') + 1;
        $slide->link_slide_id = $linkSlideID;
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

    public static function findSlide(int $id)
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

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            Story::updateSlideNumber($this->story_id);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        Story::updateSlideNumber($this->story_id);
        if ($this->isQuestion()) {
            StoryStoryTest::deleteStoryTests($this->story_id);
        }
        parent::afterDelete();
    }

    public function isLink(): bool
    {
        return (int)$this->kind === self::KIND_LINK;
    }

    public function isQuestion(): bool
    {
        return (int)$this->kind === self::KIND_QUESTION;
    }

    public function blockArray()
    {
        return array_map(function(StorySlideBlock $block) {
            return [
                'id' => $block->id,
                'type' => 'button',
                'title' => $block->title,
            ];
        }, $this->storySlideBlocks);
    }

    public function isHidden()
    {
        return (int) $this->status === self::STATUS_HIDDEN;
    }

    public function updateData(string $data): void
    {
        $this->data = $data;
        $this->save(false);
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
}

