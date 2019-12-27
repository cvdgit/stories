<?php

namespace common\models;

use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "story_slide_image".
 *
 * @property int $id
 * @property string $hash
 * @property string $collection_id
 * @property string $source_url
 * @property string $content_url
 * @property string $folder
 * @property int $created_at
 * @property int $updated_at
 * @property int $slide_id
 *
 * @property StorySlide $slide
 */
class StorySlideImage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_slide_image';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['hash', 'folder', 'slide_id'], 'required'],
            //[['created_at', 'updated_at', 'slide_id'], 'integer'],
            //[['hash', 'collection_id', 'source_url', 'folder', 'content_url'], 'string', 'max' => 255],
            //[['hash'], 'unique'],
            //[['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
            'collection_id' => 'Collection ID',
            'source_url' => 'Source Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'folder' => 'Локальная папка',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    public static function findByHash(string $hash)
    {
        if (($model = self::findOne(['hash' => $hash])) !== null) {
            return $model;
        }
        throw new DomainException('Изображение не найдено');
    }

    public static function createImage(int $slideID, string $collectionID, string $hash, string $folder, string $contentUrl, string $sourceUrl): StorySlideImage
    {
        $image = new self;
        $image->slide_id = $slideID;
        $image->collection_id = $collectionID;
        $image->hash = $hash;
        $image->folder = $folder;
        $image->content_url = $contentUrl;
        $image->source_url = $sourceUrl;
        return $image;
    }

    public static function usedCollections(int $storyID)
    {
        $storySlidesQuery = (new Query())
            ->select(['id'])
            ->from('{{story_slide}}')
            ->where('story_id = :story', [':story' => $storyID]);
        return (new Query())
            ->select('collection_id')
            ->distinct(true)
            ->from(self::tableName())
            ->where(['in', 'slide_id', $storySlidesQuery])
            ->andWhere('collection_id IS NOT NULL')
            ->all();
    }

}
