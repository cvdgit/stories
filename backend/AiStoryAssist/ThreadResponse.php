<?php

declare(strict_types=1);

namespace backend\AiStoryAssist;

use common\helpers\SmartDate;
use JsonSerializable;

class ThreadResponse implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var array
     */
    private $payload;
    /**
     * @var int
     */
    private $updatedAt;
    /**
     * @var int|null
     */
    private $storyId;

    private function __construct(string $id, string $title, array $payload, int $updatedAt, int $storyId = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->payload = $payload;
        $this->updatedAt = $updatedAt;
        $this->storyId = $storyId;
    }

    public static function fromModel(StoryThread $model): self
    {
        return new self($model->id, $model->title, $model->payload, $model->updated_at, $model->story_id);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'messages' => $this->payload,
            'title' => $this->title,
            'updatedAt' => SmartDate::dateSmart($this->updatedAt, true),
            'storyId' => $this->storyId,
        ];
    }
}
