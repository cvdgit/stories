<?php

declare(strict_types=1);

namespace backend\Changelog;

class Changelog
{
    private $id;
    private $title;
    private $text;
    private $created;
    /**
     * @var bool
     */
    private $isNew;

    public function __construct(int $id, string $title, string $text, \DateTimeImmutable $created, bool $isNew = false)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->created = $created;
        $this->isNew = $isNew;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }
}
