<?php

declare(strict_types=1);

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 * @property string $filename
 * @property int $root_folder_id
 * @property string $mime_type
 *
 * @property StorySlide[] $slides
 * @property StorySlideImage[] $linkImages
 */
class StorySlideImage extends ActiveRecord
{
    public const STATUS_SUCCESS = 0;
    public const STATUS_ERROR = 1;

    /**
     * @throws Exception
     */
    public function init(): void
    {
        $this->hash = Yii::$app->security->generateRandomString();
        parent::init();
    }

    public static function tableName(): string
    {
        return 'story_slide_image';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function attributeLabels(): array
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
     * @throws InvalidConfigException
     */
    public function getSlides(): ActiveQuery
    {
        return $this->hasMany(StorySlide::class, ['id' => 'slide_id'])->viaTable(
            'image_slide_block',
            ['image_id' => 'id'],
        );
    }

    /**
     * @throws InvalidConfigException
     */
    public function getLinkImages(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['id' => 'link_image_id'])->viaTable('image_link', ['image_id' => 'id']);
    }

    public static function findModel(int $id): ?self
    {
        /** @var StorySlideImage|null $model */
        $model = self::findOne($id);
        return $model;
    }

    public static function findByHash(string $hash): ?self
    {
        /** @var StorySlideImage|null $model */
        $model = self::findOne(['hash' => $hash]);
        return $model;
    }

    public static function findByPath(string $folder, string $fileName): ?self
    {
        /** @var StorySlideImage|null $model */
        $model = self::findOne(['folder' => $folder, 'filename' => $fileName]);
        return $model;
    }

    public static function createImage(
        int $rootFolderID,
        string $folder,
        $filename = null,
        $mimeType = null,
        $contentUrl = null,
        $sourceUrl = null,
        $collectionAccount = null,
        $collectionID = null,
        $collectionName = null
    ): self {
        $image = new self;
        $image->root_folder_id = $rootFolderID;
        $image->folder = $folder;
        $image->filename = $filename;
        $image->mime_type = $mimeType;
        $image->content_url = $contentUrl;
        $image->source_url = $sourceUrl;
        $image->collection_account = $collectionAccount;
        $image->collection_id = $collectionID;
        $image->collection_name = $collectionName;
        return $image;
    }

    public static function create(string $imageId, string $fileName, string $folder, string $extension, int $rootFolderId = 3): self
    {
        $image = new self;
        $image->root_folder_id = $rootFolderId;
        $image->hash = $imageId;
        $image->folder = $folder;
        $image->filename = $fileName;
        $image->mime_type = $extension;
        return $image;
    }

    /*public static function usedCollections(int $storyID)
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
    }*/

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function afterDelete(): void
    {
        $filePath = Yii::getAlias('@public/admin/upload/') . $this->folder . '/' . $this->hash . '.jpeg';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        parent::afterDelete();
    }

    public function imageUrl(): string
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $this->hash]);
    }

    public function getFullPath(): string
    {
        return Yii::getAlias('@public/admin/upload/') . $this->folder . '/' . $this->hash . '.jpeg';
    }

    public function getShortPath(): string
    {
        return '/' . $this->folder . '/' . $this->hash . '.jpeg';
    }

    public function getImageName(): string
    {
        if (!empty($this->filename)) {
            return $this->filename;
        }
        return $this->hash . '.jpeg';
    }

    public function getImagePath(bool $abs = true): string
    {
        $rootFolder = Yii::$app->params['images.root'][$this->root_folder_id];
        return ($abs ? Yii::getAlias('@public') : '') . $rootFolder . $this->folder . '/' . $this->getImageName();
    }

    public function getImageThumbPath(bool $abs = false): string
    {
        $imagePath = $this->getImagePath($abs);
        return str_replace(basename($imagePath), 'thumb_' . basename($imagePath), $imagePath);
    }

    public static function findImageByPath(string $path): ?self
    {
        if (strpos($path, '://') !== false) {
            $query = parse_url($path, PHP_URL_QUERY);
            parse_str($query, $result);
            $imageHash = $result['id'];
            return self::findByHash($imageHash);
        }
        return self::findByPath(basename(dirname($path)), basename($path));
    }
}
