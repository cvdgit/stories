<?php

namespace backend\models;

use common\models\Story;
use common\models\StorySlideImage;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_story_slide_image".
 *
 * @property int $story_id
 * @property int $story_slide_image_id
 *
 * @property Story $story
 * @property StorySlideImage $storySlideImage
 */
class StoryImageLink extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_story_slide_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'story_slide_image_id'], 'required'],
            [['story_id', 'story_slide_image_id'], 'integer'],
            [['story_id', 'story_slide_image_id'], 'unique', 'targetAttribute' => ['story_id', 'story_slide_image_id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['story_slide_image_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlideImage::class, 'targetAttribute' => ['story_slide_image_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'story_id' => 'Story ID',
            'story_slide_image_id' => 'Story Slide Image ID',
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
    public function getStorySlideImage()
    {
        return $this->hasOne(StorySlideImage::class, ['id' => 'story_slide_image_id']);
    }

    public static function create(int $storyID, int $imageID): self
    {
        $model = new self();
        $model->story_id = $storyID;
        $model->story_slide_image_id = $imageID;
        return $model;
    }
}
