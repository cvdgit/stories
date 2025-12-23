<?php

declare(strict_types=1);

namespace modules\edu\query;

use JsonSerializable;

final class Lesson implements JsonSerializable
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $stories;

    private function __construct(int $id, string $name, array $stories)
    {
        $this->id = $id;
        $this->name = $name;
        $this->stories = $stories;
    }

    public static function fromPayload(array $lesson): self
    {
        return new self((int) $lesson['lessonId'], $lesson['lessonName'], $lesson['stories']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addStoryId(int $storyId): void
    {
        if (!in_array($storyId, $this->stories, true)) {
            $this->stories[] = $storyId;
        }
    }

    public function haveStories(): bool
    {
        return count($this->stories) > 0;
    }

    public function jsonSerialize(): array
    {
        return [
            'lessonId' => $this->id,
            'lessonName' => $this->name,
            'stories' => $this->stories,
        ];
    }
}
