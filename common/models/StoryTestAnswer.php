<?php

namespace common\models;

use DomainException;
use Yii;
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
 *
 * @property StoryTestQuestion $storyQuestion
 */
class StoryTestAnswer extends ActiveRecord
{

    const CORRECT_ANSWER = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_test_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['order', 'is_correct'], 'integer'],
            [['name', 'image', 'description'], 'string', 'max' => 255],
            //[['story_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTestQuestion::class, 'targetAttribute' => ['story_question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_question_id' => 'Вопрос',
            'name' => 'Ответ',
            'order' => 'Order',
            'is_correct' => 'Ответ правильный',
            'image' => 'Изображение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryQuestion()
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

    public function answerIsCorrect()
    {
        return (int)$this->is_correct === self::CORRECT_ANSWER;
    }

    public static function create(int $questionID, string $name, int $isCorrect, int $order = null, string $image = null): StoryTestAnswer
    {
        $model = new self;
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

    public static function createFromRelation(string $name, int $isCorrect, string $description = null): self
    {
        $model = new self();
        $model->name = $name;
        $model->is_correct = $isCorrect;
        $model->description = $description;
        return $model;
    }

    public static function createFromRegion(int $questionID, string $name, int $isCorrect, string $regionID)
    {
        $model = self::create($questionID, $name, $isCorrect);
        $model->region_id = $regionID;
        $model->save();
        return $model->id;
    }

    public function haveImage()
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

    public function getOrigImagePath(): string
    {
        if ($this->image === null) {
            return '';
        }
        return '/test_images/' . str_replace('thumb_', '', $this->image);
    }

    public function getImagesPath()
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
        if ($order === null) {
            $max = (new Query())
                ->from(self::tableName())
                ->where('story_question_id = :id', [':id' => $questionID])
                ->max('`order`');
            $order = 1 + $max;
        }
        return self::create($questionID, $name, true, $order);
    }

    public function afterDelete()
    {
        $this->deleteImage();
        parent::afterDelete();
    }
}
