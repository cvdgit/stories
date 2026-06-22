<?php

declare(strict_types=1);

namespace backend\Retelling;

use JsonSerializable;

class RetellingSettings implements JsonSerializable
{
    /**
     * @var int
     */
    private $threshold;

    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    public static function fromArray(array $payload): self
    {
        return new self((int) $payload['threshold']);
    }

    public function asArray(): array
    {
        return [
            'threshold' => $this->threshold,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}
