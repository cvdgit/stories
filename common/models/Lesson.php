<?php

namespace common\models;

use backend\components\course\LessonType;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lesson".
 *
 * @property int $id
 * @property string $name
 * @property int $story_id
 * @property int $order
 * @property string $uuid
 * @property int $type
 *
 * @property LessonBlock[] $lessonBlocks
 * @property LessonBlockQuiz[] $lessonBlocksQuiz
 * @property StorySlide[] $slides
 * @property Story $story
 */
class Lesson extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'story_id', 'uuid'], 'required'],
            [['story_id', 'order', 'type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['uuid'], 'string', 'max' => 36],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'story_id' => 'Story ID',
            'order' => 'Order',
        ];
    }

    /**
     * Gets query for [[LessonBlocks]].
     *
     * @return ActiveQuery
     */
    public function getLessonBlocks(): ActiveQuery
    {
        return $this->hasMany(LessonBlock::class, ['lesson_id' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }

    /**
     * Gets query for [[LessonQuiz]].
     *
     * @return ActiveQuery
     */
    public function getLessonBlocksQuiz(): ActiveQuery
    {
        return $this->hasMany(LessonBlockQuiz::class, ['lesson_id' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }

    /**
     * Gets query for [[Slides]].
     *
     * @return ActiveQuery
     * @throws yii\base\InvalidConfigException
     */
    public function getSlides(): ActiveQuery
    {
        return $this->hasMany(StorySlide::class, ['id' => 'slide_id'])
            ->viaTable('lesson_block', ['lesson_id' => 'id'])
            ->innerJoin('lesson_block', 'lesson_block.lesson_id = :lesson AND lesson_block.slide_id = story_slide.id', [':lesson' => $this->id])
            ->orderBy(['lesson_block.order' => SORT_ASC]);
    }

    /**
     * Gets query for [[Story]].
     *
     * @return ActiveQuery
     */
    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    public static function findOneByUUID(string $uuid): ?self
    {
        return self::find()->where(['uuid' => $uuid])->one();
    }

    /**
     * @throws \Exception
     */
    public function updateBlocksOrder(): void
    {
        self::getDb()->transaction(function() {
            $order = 1;
            foreach ($this->lessonBlocks as $block) {
                $block->updateOrder($order);
                $block->save(false, ['order']);
            }
        });
    }

    public function typeIsQuiz(): bool
    {
        return LessonType::typeIsQuiz($this->type);
    }

    public function typeIsBlocks(): bool
    {
        return LessonType::typeIsBlocks($this->type);
    }

    public static function findByOrder(int $storyId, int $order): ?self
    {
        return self::find()->where(['story_id' => $storyId, 'order' => $order])->one();
    }

    public function createQuizBlock(int $slideId, int $quizId, int $order = 1): LessonBlockQuiz
    {
        $lessonBlock = LessonBlockQuiz::create($this->id, $slideId, $quizId, $order);
        if (!$lessonBlock->save()) {
            throw new \DomainException('LessonBlockQuiz save exception');
        }
        return $lessonBlock;
    }

    public function updateTypeQuiz(): void
    {
        $this->type = LessonType::QUIZ;
        if (!$this->save(false, ['type'])) {
            throw new \DomainException('Lesson::updateTypeQuiz exception');
        }
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        if (!$this->save(false, ['name'])) {
            throw new \DomainException('Lesson::updateName exception');
        }
    }

    public static function create(string $uuid, int $storyId, string $name, int $type, int $order): self
    {
        $model = new self();
        $model->uuid = $uuid;
        $model->story_id = $storyId;
        $model->name = $name;
        $model->type = $type;
        $model->order = $order;
        return $model;
    }
}
