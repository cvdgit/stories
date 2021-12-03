<?php

namespace common\models\story;

use common\components\BaseStatus;
use common\models\Story;
use yii\db\Exception;

class StoryStatus extends BaseStatus
{

    public const DRAFT = 0;
    public const PUBLISHED = 1;
    public const FOR_PUBLICATION = 2;
    public const TASK = 3;

    private $status;

    public function __construct(int $status)
    {
        if (!isset(self::asArray()[$status])) {
            throw new Exception('Unknown status');
        }
        $this->status = $status;
    }

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

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::PUBLISHED;
    }

    public function isForPublication(): bool
    {
        return $this->status === self::FOR_PUBLICATION;
    }
}
