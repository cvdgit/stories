<?php

declare(strict_types=1);

namespace backend\modules\LearningPath\Create;

use Ramsey\Uuid\UuidInterface;

class CreateLearningPathCommand
{
    /**
     * @var UuidInterface
     */
    private $uuid;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $payload;
    /**
     * @var int
     */
    private $userId;

    public function __construct(UuidInterface $uuid, string $name, array $payload, int $userId) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->payload = $payload;
        $this->userId = $userId;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
