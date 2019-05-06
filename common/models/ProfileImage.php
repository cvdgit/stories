<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "profile_image".
 *
 * @property int $id
 * @property int $profile_id
 * @property string $file
 *
 * @property Profile $profile
 */
class ProfileImage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%profile_image}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'file',
                'createThumbsOnRequest' => false,
                'filePath' => '@public/photo/[[attribute_profile_id]]/[[id]].[[extension]]',
                'fileUrl' => '/photo/[[attribute_profile_id]]/[[id]].[[extension]]',
                'thumbPath' => '@public/photo/[[attribute_profile_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbUrl' => '/photo/[[attribute_profile_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbs' => [
                    'profile' => ['width' => 192, 'height' => 192],
                    'list' => ['width' => 44, 'height' => 44],
                ],
            ],
        ];
    }

    public static function create(UploadedFile $file): self
    {
        $photo = new static();
        $photo->file = $file;
        return $photo;
    }

}
