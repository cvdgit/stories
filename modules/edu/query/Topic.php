<?php

declare(strict_types=1);

namespace modules\edu\query;

use JsonSerializable;
use modules\edu\components\ArrayHelper;

final class Topic implements JsonSerializable
{
    /**
     * @var int
     */
    private $id;
    private $name;
    private $lessons;

    private function __construct(int $id, string $name, array $lessons = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->lessons = array_map(static function (array $lesson): Lesson {
            return Lesson::fromPayload($lesson);
        }, $lessons);
    }

    public static function fromPayload(array $topic): self
    {
        return new self((int) $topic['topicId'], $topic['topicName'], $topic['lessons']);
    }

    public function addLesson(Lesson $lesson): void
    {
        $this->lessons[] = $lesson;
    }

    public function findLesson(int $id): ?Lesson
    {
        return ArrayHelper::array_find($this->lessons, static function (Lesson $lesson) use ($id): bool {
            return $lesson->getId() === $id;
        });
    }

    public function jsonSerialize(): array
    {
        return [
            'topicId' => $this->id,
            'topicName' => $this->name,
            'lessons' => $this->lessons,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLessonsWithStories(): array
    {
        return array_values(
            array_filter($this->lessons, static function (Lesson $lesson): bool {
                return $lesson->haveStories();
            }),
        );
    }

    public function haveLessons(): bool
    {
        return count($this->lessons) > 0;
    }

    public function setLessons(array $lessons): void
    {
        $this->lessons = $lessons;
    }
}
