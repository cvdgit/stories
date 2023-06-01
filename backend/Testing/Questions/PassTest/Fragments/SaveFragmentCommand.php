<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest\Fragments;

class SaveFragmentCommand
{
    private $userId;
    private $name;
    private $items;

    public function __construct(int $userId, string $name, array $items)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->items = $items;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<array-key, array{name: string}>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
