<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory;

use yii\db\ActiveRecord;

class RequiredStoryModel extends ActiveRecord
{
    public const STATUS_ACTIVE = 'active';

    public static function tableName(): string
    {
        return '{{%edu_required_story}}';
    }
}
