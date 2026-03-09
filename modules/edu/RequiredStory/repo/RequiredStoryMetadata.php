<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use JsonSerializable;

class RequiredStoryMetadata implements JsonSerializable
{
    /**
     * @var int
     */
    private $total;
    /**
     * @var array
     */
    private $chunks;

    public function __construct(int $total, array $chunks)
    {
        $this->total = $total;
        $this->chunks = $chunks;
    }

    public static function fromArray(array $metadata): self
    {
        return new self((int) $metadata['total'], $metadata['chunks']);
    }

    public function getChunks(): array
    {
        return $this->chunks;
    }

    public function setChunks(array $chunks): void
    {
        $this->chunks = $chunks;
    }

    public function getCurrentPlan(int $fact): int
    {
        $plan = 0;
        foreach ($this->getChunks() as $chunk) {
            $n = (int) $chunk['n'];
            $fact -= $n;
            if ($fact <= 0) {
                $plan = $n;
                break;
            }
        }
        return $plan;
    }

    public function jsonSerialize(): array
    {
        return [
            'total' => $this->total,
            'chunks' => $this->chunks,
        ];
    }
}
