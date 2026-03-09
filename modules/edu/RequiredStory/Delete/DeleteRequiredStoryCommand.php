<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\Delete;

use Ramsey\Uuid\UuidInterface;

class DeleteRequiredStoryCommand
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var int
     */
    private $userId;

    public function __construct(UuidInterface $id, int $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
