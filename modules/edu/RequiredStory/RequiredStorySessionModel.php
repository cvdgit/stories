<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory;

use yii\db\ActiveRecord;

class RequiredStorySessionModel extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%edu_required_story_session}}';
    }
}
