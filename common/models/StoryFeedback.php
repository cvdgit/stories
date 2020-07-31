<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
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
 *
 * @property Story $story
 * @property User $assignUser
 */
class StoryFeedback extends ActiveRecord
{

    const STATUS_NEW = 0;
    const STATUS_DONE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_feedback';
    }

    public function behaviors()
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
    public function rules()
    {
        return [
            [['story_id', 'assign_user_id', 'slide_number', 'slide_id'], 'required'],
            [['story_id', 'assign_user_id', 'slide_number', 'status', 'slide_id'], 'integer'],
            [['text'], 'string', 'max' => 255],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['assign_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['assign_user_id' => 'id']],
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
            'assign_user_id' => 'Assign User ID',
            'slide_number' => 'Номер слайда',
            'text' => 'Текст',
            'status' => 'Статус',
            'created_at' => 'Создано',
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
    public function getAssignUser()
    {
        return $this->hasOne(User::class, ['id' => 'assign_user_id']);
    }

    public static function createFeedback(StorySlide $slide)
    {
        $model = new self;
        $model->story_id = $slide->story_id;
        $model->slide_id = $slide->id;
        $model->slide_number = $slide->number;
        $model->assign_user_id = 1;
        return $model->save();
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => 'Новая',
            self::STATUS_DONE => 'Исправлена',
        ];
    }

    public function getStatusText()
    {
        $arr = self::getStatusList();
        return $arr[$this->status];
    }

    public static function updateStatus($ids)
    {
        return self::updateAll(['status' => self::STATUS_DONE], ['IN', 'id', $ids]);
    }

}
