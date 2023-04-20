<?php

declare(strict_types=1);

namespace backend\Changelog;

class Changelog
{
    private $title;
    private $text;
    private $created;

    public function __construct(string $title, string $text, \DateTimeImmutable $created)
    {
        $this->title = $title;
        $this->text = $text;
        $this->created = $created;
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
}
