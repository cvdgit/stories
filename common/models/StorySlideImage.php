<?php

namespace common\models;

use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
            [['hash', 'folder'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['hash', 'collection_id', 'source_url', 'folder', 'content_url'], 'string', 'max' => 255],
            [['hash'], 'unique'],
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

    public static function findByHash(string $hash)
    {
        if (($model = self::findOne(['hash' => $hash])) !== null) {
            return $model;
        }
        throw new DomainException('Изображение не найдено');
    }

}
