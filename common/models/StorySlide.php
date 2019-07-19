<?php

namespace common\models;

use Yii;

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
 *
 * @property Story $story
 */
class StorySlide extends \yii\db\ActiveRecord
{

    const STATUS_VISIBLE = 1;
    const STATUS_HIDDEN = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_slide';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'data', 'number', 'created_at', 'updated_at'], 'required'],
            [['story_id', 'number', 'status', 'created_at', 'updated_at'], 'integer'],
            [['data'], 'string'],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::className(), 'targetAttribute' => ['story_id' => 'id']],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::className(), ['id' => 'story_id']);
    }

    public static function findSlide(int $storyID, int $slideNumber)
    {
        return self::find()
            ->where('story_id = :story', [':story' => $storyID])
            ->andWhere('number = :number', [':number' => $slideNumber])
            ->one();
    }

    public static function deleteSlide(int $storyID, int $slideNumber)
    {
        $slide = self::findSlide($storyID, $slideNumber);
        return $slide->delete();
    }

    public static function findFirstSlide(int $storyID)
    {
        return self::find()
            ->where('story_id = :story', [':story' => $storyID])
            ->orderBy(['number' => SORT_ASC])
            ->limit(1)
            ->one();
    }

}
