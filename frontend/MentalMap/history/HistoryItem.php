<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use Ramsey\Uuid\UuidInterface;

class HistoryItem
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var bool
     */
    private $done;
    /**
     * @var int
     */
    private $all;
    /**
     * @var int
     */
    private $allTextClosed;
    /**
     * @var int
     */
    private $allTextClosedPrev;
    /**
     * @var int
     */
    private $hiding;
    /**
     * @var int
     */
    private $hidingPrev;
    /**
     * @var int
     */
    private $seconds;
    /**
     * @var int
     */
    private $target;

    public function __construct(
        UuidInterface $id,
        bool $done = false,
        int $all = 0,
        int $allTextClosed = 0,
        int $allTextClosedPrev = 0,
        int $hiding = 0,
        int $hidingPrev = 0,
        int $seconds = 0,
        int $target = 0
    ) {
        $this->id = $id;
        $this->done = $done;
        $this->all = $all;
        $this->allTextClosed = $allTextClosed;
        $this->allTextClosedPrev = $allTextClosedPrev;
        $this->hiding = $hiding;
        $this->hidingPrev = $hidingPrev;
        $this->seconds = $seconds;
        $this->target = $target;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'done' => $this->done,
            'all' => $this->all,
            'allTextClosed' => $this->allTextClosed,
            'allTextClosedPrev' => $this->allTextClosedPrev,
            'hiding' => $this->hiding,
            'hidingPrev' => $this->hidingPrev,
            'target' => $this->target,
            'seconds' => $this->seconds,
        ];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
