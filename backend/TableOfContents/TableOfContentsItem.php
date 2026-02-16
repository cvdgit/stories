<?php

declare(strict_types=1);

namespace backend\TableOfContents;

use Ramsey\Uuid\UuidInterface;

class TableOfContentsItem
{
    /**
     * @var int
     */
    private $slideId;
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var TableOfContentsPayload
     */
    private $payload;

    public function __construct(int $slideId, UuidInterface $id, TableOfContentsPayload $payload)
    {
        $this->slideId = $slideId;
        $this->id = $id;
        $this->payload = $payload;
    }

    public function getSlideId(): int
    {
        return $this->slideId;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPayload(): TableOfContentsPayload
    {
        return $this->payload;
    }
}
