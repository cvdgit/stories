<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogUpdate;

class UpdateChangelogCommand
{
    private $id;
    private $title;
    private $text;
    private $tags;
    private $status;

    public function __construct(int $id, string $title, string $text, int $status, string $tags = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->status = $status;
        $this->tags = $tags;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
