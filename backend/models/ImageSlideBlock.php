<?php

namespace backend\models;

use common\models\StorySlide;
use common\models\StorySlideImage;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "image_slide_block".
 *
 * @property int $image_id
 * @property int $slide_id
 * @property string $block_id
 * @property int $deleted
 *
 * @property StorySlideImage $image
 * @property StorySlide $slide
 */
class ImageSlideBlock extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image_slide_block';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_id', 'slide_id', 'block_id'], 'required'],
            [['image_id', 'slide_id', 'deleted'], 'integer'],
            [['block_id'], 'string', 'max' => 255],
            [['image_id', 'slide_id', 'block_id'], 'unique', 'targetAttribute' => ['image_id', 'slide_id', 'block_id']],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlideImage::className(), 'targetAttribute' => ['image_id' => 'id']],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::className(), 'targetAttribute' => ['slide_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'image_id' => 'Image ID',
            'slide_id' => 'Slide ID',
            'block_id' => 'Block ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(StorySlideImage::class, ['id' => 'image_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    public static function create(int $imageID, int $slideID, string $blockID, int $deleted = 0): self
    {
        $model = new self();
        $model->image_id = $imageID;
        $model->slide_id = $slideID;
        $model->block_id = $blockID;
        $model->deleted = $deleted;
        return $model;
    }

    public static function deleteSlideLink(int $slideID, string $blockID): void
    {
        self::deleteAll('slide_id = :slide AND block_id = :block', [':slide' => $slideID, ':block' => $blockID]);
    }

    public static function deleteImageBlock(int $slideID, string $blockID): void
    {
        $command = Yii::$app->db->createCommand();
        $command->update(self::tableName(), ['deleted' => 1], 'slide_id = :slide AND block_id = :block', [':slide' => $slideID, ':block' => $blockID]);
        $command->execute();
    }

    public static function removeDeletedLink(int $imageID, int $slideID): void
    {
        self::deleteAll('slide_id = :slide AND image_id = :image AND deleted = 1', [':slide' => $slideID, ':image' => $imageID]);
    }
}
