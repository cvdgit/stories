<?php

namespace common\models\story_feedback;

use common\components\BaseStatus;

class StoryFeedbackStatus extends BaseStatus
{

    public const STATUS_NEW = 0;
    public const STATUS_DONE = 1;

    public static function asArray(): array
    {
        return [
            self::STATUS_NEW => 'Новая',
            self::STATUS_DONE => 'Исправлена',
        ];
    }

    public static function statusIsNew(int $status): bool
    {
        return $status === self::STATUS_NEW;
    }

    public static function statusIsDone(int $status): bool
    {
        return $status === self::STATUS_DONE;
    }
}
