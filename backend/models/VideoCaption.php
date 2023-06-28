<?php

declare(strict_types=1);

namespace backend\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property int $video_id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $lang [varchar(32)]
 * @property string $content
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 */
class VideoCaption extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName(): string
    {
        return 'video_caption';
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function create(int $videoId, string $name, string $lang, string $content): self
    {
        $model = new self();
        $model->video_id = $videoId;
        $model->name = $name;
        $model->lang = $lang;
        $model->content = $content;
        return $model;
    }
}
