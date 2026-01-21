<?php

declare(strict_types=1);

namespace modules\edu\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $story_id
 * @property string $data
 * @property int $number
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $kind
 * @property int $link_slide_id
 */
class EduStorySlide extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%story_slide}}';
    }
}
