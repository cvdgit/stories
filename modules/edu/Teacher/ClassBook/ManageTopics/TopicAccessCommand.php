<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\ManageTopics;

class TopicAccessCommand
{
    private $classBookId;
    private $items;

    public function __construct(int $classBookId, array $items)
    {
        $this->classBookId = $classBookId;
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function getClassBookId(): int
    {
        return $this->classBookId;
    }

    /**
     * @return TopicAccessForm[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
