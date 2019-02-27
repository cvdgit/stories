<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 *
 * @property Story $story
 * @property User $assignUser
 */
class StoryFeedback extends \yii\db\ActiveRecord
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
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
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
            [['story_id', 'assign_user_id', 'slide_number'], 'required'],
            [['story_id', 'assign_user_id', 'slide_number', 'status', 'created_at'], 'integer'],
            [['text'], 'string', 'max' => 255],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::className(), 'targetAttribute' => ['story_id' => 'id']],
            [['assign_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['assign_user_id' => 'id']],
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
        return $this->hasOne(Story::className(), ['id' => 'story_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignUser()
    {
        return $this->hasOne(User::className(), ['id' => 'assign_user_id']);
    }

    public static function createFeedback($story_id, $slide_number)
    {
        $model = new self;
        $model->story_id = $story_id;
        $model->slide_number = $slide_number;
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
