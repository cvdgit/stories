<?php

namespace common\models\story;

use common\components\BaseStatus;
use common\models\Story;

class StoryStatus extends BaseStatus
{

    public const DRAFT = 0;
    public const PUBLISHED = 1;
    public const FOR_PUBLICATION = 2;
    public const TASK = 3;

    public static function asArray(): array
    {
        return [
            self::DRAFT => 'Черновик',
            self::PUBLISHED => 'Опубликован',
            self::FOR_PUBLICATION => 'На публикацию',
            self::TASK => 'Задание',
        ];
    }

    public static function isTask(Story $story): bool
    {
        return $story->status === self::TASK;
    }
}
