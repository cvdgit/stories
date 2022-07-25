<?php

namespace common\models\story_feedback;

use common\models\Story;
use common\models\StorySlide;
use common\models\User;
use frontend\models\feedback\CreateFeedbackForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_feedback".
 *
 * @property int $id
 * @property int $story_id
 * @property int $assign_user_id
 * @property int $slide_number
 * @property string $text
 * @property int $status
 * @property int $created_at
 * @property int $slide_id
 * @property int $testing_id
 * @property int $question_id
 *
 * @property Story $story
 * @property User $assignUser
 */
class StoryFeedback extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'story_feedback';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['story_id', 'assign_user_id', 'slide_number', 'slide_id'], 'required'],
            [['story_id', 'assign_user_id', 'slide_number', 'status', 'slide_id', 'testing_id', 'question_id'], 'integer'],
            [['text'], 'string', 'max' => 1024],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['assign_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['assign_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'story_id' => 'Story ID',
            'assign_user_id' => 'Assign User ID',
            'slide_number' => 'Номер слайда',
            'text' => 'Текст',
            'status' => 'Статус',
            'created_at' => 'Создано',
        ];
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    public function getAssignUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'assign_user_id']);
    }

    public static function updateStatus($ids): int
    {
        return self::updateAll(['status' => StoryFeedbackStatus::STATUS_DONE], ['IN', 'id', $ids]);
    }

    public function setStatusDone(): void
    {
        $this->status = StoryFeedbackStatus::STATUS_DONE;
    }

    public static function create(CreateFeedbackForm $form): self
    {
        $model = new self();
        $model->story_id = $form->story_id;
        $model->slide_id = $form->slide_id;
        $model->slide_number = 1;
        $model->assign_user_id = 1;
        $model->text = $form->text;
        $model->testing_id = $form->testing_id;
        $model->question_id = $form->question_id;
        return $model;
    }
}
