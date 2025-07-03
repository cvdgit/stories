<?php

namespace common\models;

use common\helpers\Url;
use DomainException;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "story_test_answer".
 *
 * @property int $id
 * @property int $story_question_id
 * @property string $name
 * @property int $order
 * @property int $is_correct
 * @property string $image
 * @property string $region_id
 * @property string $description
 * @property int $hidden
 *
 * @property StoryTestQuestion $storyQuestion
 */
class StoryTestAnswer extends ActiveRecord
{

    public const INCORRECT_ANSWER = 0;
    public const CORRECT_ANSWER = 1;
    public const HIDDEN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'story_test_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            //[['name'], 'required'],
            [['order', 'is_correct', 'hidden'], 'integer'],
            [['name'], 'string', 'max' => 512],
            [['image'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'story_question_id' => 'Вопрос',
            'name' => 'Ответ',
            'order' => 'Order',
            'is_correct' => 'Верный',
            'image' => 'Изображение',
        ];
    }

    public function getStoryQuestion(): ActiveQuery
    {
        return $this->hasOne(StoryTestQuestion::class, ['id' => 'story_question_id']);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Ответ не найден');
    }

    public function answerIsCorrect(): bool
    {
        return (int)$this->is_correct === self::CORRECT_ANSWER;
    }

    public static function create(int $questionID, string $name, int $isCorrect, int $order = null, string $image = null): StoryTestAnswer
    {
        $model = new self();
        $model->story_question_id = $questionID;
        $model->name = $name;
        $model->is_correct = $isCorrect;
        if ($order !== null) {
            $model->order = $order;
        }
        if ($image !== null) {
            $model->image = $image;
        }
        return $model;
    }

    public function updateAnswer(string $name, int $isCorrect): void
    {
        $this->name = $name;
        $this->is_correct = $isCorrect;
    }

    public function updateAnswerImage(string $image = null): void
    {
        $this->image = $image;
    }

    public static function createFromRelation(string $name, int $isCorrect, string $description = null): self
    {
        $model = new self();
        $model->name = $name;
        $model->is_correct = $isCorrect;
        $model->description = $description;
        return $model;
    }

    public static function createSequenceFromRelation(string $name, int $order): self
    {
        $model = new self();
        $model->name = $name;
        $model->is_correct = self::CORRECT_ANSWER;
        $model->order = $order;
        return $model;
    }

    public static function createFromRegion(int $questionID, string $name, int $isCorrect, string $regionID): int
    {
        $model = self::create($questionID, $name, $isCorrect);
        $model->region_id = $regionID;
        $model->save();
        return $model->id;
    }

    public function haveImage(): bool
    {
        return !empty($this->image);
    }

    public function getImagePath(): string
    {
        if ($this->image === null) {
            return '';
        }
        return '/test_images/' . $this->image;
    }

    public function getImageUrl(): string
    {
        if (($path = $this->getImagePath()) !== '') {
            return Url::homeUrl() . $path;
        }
        return '';
    }

    public function getOrigImagePath(): string
    {
        if ($this->image === null) {
            return '';
        }
        return '/test_images/' . str_replace('thumb_', '', $this->image);
    }

    public function getOrigImageUrl(): string
    {
        if (($path = $this->getOrigImagePath()) !== '') {
            return Url::homeUrl() . $path;
        }
        return '';
    }

    public function getImagesPath(): string
    {
        return Yii::getAlias('@public') . '/test_images/';
    }

    public function deleteImage(): void
    {
        $images = [
            $this->getImagePath(),
            $this->getOrigImagePath(),
        ];
        foreach ($images as $imagePath) {
            if (!empty($imagePath) && (file_exists($path = Yii::getAlias('@public') . $imagePath))) {
                FileHelper::unlink($path);
            }
        }
    }

    public static function createSequenceAnswer(int $questionID, string $name, int $order = null): self
    {
        $answerExists = (new Query())
            ->from(self::tableName())
            ->where(['story_question_id' => $questionID])
            ->andWhere(['name' => $name])
            ->exists();
        if ($answerExists) {
            throw new DomainException('Такой ответ уже добавлен в вопрос');
        }
        if ($order === null) {
            $max = (new Query())
                ->from(self::tableName())
                ->where('story_question_id = :id', [':id' => $questionID])
                ->max('`order`');
            $order = 1 + (int)$max;
        }
        return self::create($questionID, $name, true, $order);
    }

    public function afterDelete()
    {
        $this->deleteImage();
        parent::afterDelete();
    }

    public function answerSetHidden(): void
    {
        $this->hidden = self::HIDDEN;
    }

    public static function getCreateAnswerRoute(int $questionId): array
    {
        return ['test/create-answer', 'question_id' => $questionId];
    }

    public static function createFromAnswer(StoryTestAnswer $answer, int $questionId): self
    {
        $newAnswer = new self();
        $newAnswer->attributes = $answer->attributes;
        $newAnswer->story_question_id = $questionId;
        return $newAnswer;
    }

    public static function findAnswer(int $questionId, int $answerId): ?StoryTestAnswer
    {
        /** @var StoryTestAnswer|null $model */
        $model = self::find()->andWhere([
            'id' => $answerId,
            'story_question_id' => $questionId,
        ])->one();
        return $model;
    }

    public static function findAnswerByRegionId(string $id): ?StoryTestAnswer
    {
        /** @var StoryTestAnswer|null $model */
        $model = self::find()->andWhere([
            'region_id' => $id,
        ])->one();
        return $model;
    }
}
