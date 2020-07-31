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
 * @property string $collection_account
 * @property string $collection_id
 * @property string $collection_name
 * @property string $source_url
 * @property string $content_url
 * @property string $folder
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 *
 * @property StorySlide[] $slides
 * @property StorySlideImage[] $linkImages
 */
class StorySlideImage extends ActiveRecord
{

    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

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
        return [];
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getSlides()
    {
        return $this->hasMany(StorySlide::class, ['id' => 'slide_id'])->viaTable('image_slide_block', ['image_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkImages()
    {
        return $this->hasMany(__CLASS__, ['id' => 'link_image_id'])->viaTable('image_link', ['image_id' => 'id']);
    }

    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Изображение не найдено');
    }

    public static function findByHash(string $hash)
    {
        if (($model = self::findOne(['hash' => $hash])) !== null) {
            return $model;
        }
        throw new DomainException('Изображение не найдено');
    }

    public static function createImage(string $collectionAccount, string $collectionID, string $collectionName, string $hash, string $folder, string $contentUrl, string $sourceUrl): StorySlideImage
    {
        $image = new self;
        $image->collection_account = $collectionAccount;
        $image->collection_id = $collectionID;
        $image->collection_name = $collectionName;
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
            ->select(['collection_account', 'collection_id', 'collection_name'])
            ->distinct(true)
            ->from('{{%image_slide_block}}')
            ->where(['in', 'slide_id', $storySlidesQuery])
            ->innerJoin('{{%story_slide_image}}', '{{%story_slide_image}}.id = {{%image_slide_block}}.image_id')
            ->andWhere('collection_id IS NOT NULL')
            ->all();
    }

    public static function storyImages(int $storyID)
    {
        $storySlidesQuery = (new Query())
            ->select(['id'])
            ->from('{{story_slide}}')
            ->where('story_id = :story', [':story' => $storyID]);
        return (new Query())
            ->select([
                '{{%story_slide_image}}.*',
                '(SELECT COUNT(image_link.image_id) FROM image_link WHERE image_link.image_id = image_slide_block.image_id) AS link_image_count',
                '{{%image_slide_block}}.slide_id',
                '{{%image_slide_block}}.block_id',
            ])
            ->from('{{%image_slide_block}}')
            ->where(['in', 'slide_id', $storySlidesQuery])
            ->innerJoin('{{%story_slide_image}}', '{{%story_slide_image}}.id = {{%image_slide_block}}.image_id')
            ->all();
    }

    public function isSuccess()
    {
        return (int)$this->status === self::STATUS_SUCCESS;
    }

    public function afterDelete()
    {
        $filePath = Yii::getAlias('@public/admin/upload/') . $this->folder . '/' . $this->hash . '.jpeg';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        parent::afterDelete();
    }

    public function imageUrl()
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $this->hash]);
    }

    public function getFullPath()
    {
        return Yii::getAlias('@public/admin/upload/') . $this->folder . '/' . $this->hash . '.jpeg';
    }

    public function getShortPath()
    {
        return '/' . $this->folder . '/' . $this->hash . '.jpeg';
    }

}
