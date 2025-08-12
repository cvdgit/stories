<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyMentalMap;

use Ramsey\Uuid\UuidInterface;

class CopyMentalMapCommand
{
    /**
     * @var UuidInterface
     */
    private $newId;
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $userId;

    public function __construct(UuidInterface $newId, UuidInterface $id, string $name, int $userId)
    {
        $this->newId = $newId;
        $this->id = $id;
        $this->name = $name;
        $this->userId = $userId;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getNewId(): UuidInterface
    {
        return $this->newId;
    }
}
