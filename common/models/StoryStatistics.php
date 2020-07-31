<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_statistics".
 *
 * @property int $id
 * @property int $story_id
 * @property int $slide_number
 * @property int $begin_time
 * @property int $end_time
 * @property int $chars
 * @property int $created_at
 * @property string $session
 * @property int $slide_id
 * @property int $user_id
 *
 * @property Story $story
 */
class StoryStatistics extends ActiveRecord
{

    public $slide_time = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_statistics';
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
            [['story_id', 'slide_id', 'begin_time', 'end_time', 'chars', 'session'], 'required'],
            [['story_id', 'slide_number', 'begin_time', 'end_time', 'chars', 'slide_id', 'user_id'], 'integer'],
            [['session'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => 'ИД истории',
            'slide_number' => 'Номер слайда',
            'begin_time' => 'Время начала просмотра слайда',
            'end_time' => 'Время окончания просмотра слайда',
            'chars' => 'Количество символов на слайде',
            'created_at' => 'Дата события',
            'session' => 'Сессия',
            'slide_time' => 'Время, на слайде',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    public static function findStoryStatistics($story_id)
    {
        return self::find()->andWhere(['{{%story_statistics}}.story_id' => $story_id]);
    }

}
