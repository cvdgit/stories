<?php

declare(strict_types=1);

namespace backend\TableOfContents;

use JsonSerializable;
use Ramsey\Uuid\Uuid;

class TableOfContentsPayload implements JsonSerializable
{
    private $title;
    /** @var array<TableOfContentsGroup> */
    private $groups = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function fromPayload(array $payload): self
    {
        $self = new self($payload['title']);
        foreach ($payload['groups'] as $payloadGroup) {
            $group = new TableOfContentsGroup(Uuid::fromString($payloadGroup['id']), $payloadGroup['name']);
            foreach ($payloadGroup['slides'] as $groupSlide) {
                $group->addSlide(
                    (int) $groupSlide['id'],
                    $groupSlide['title'],
                    (int) $groupSlide['slideNumber'],
                    Uuid::fromString($groupSlide['cardId']),
                );
            }
            foreach ($payloadGroup['cards'] as $groupCard) {
                $group->addCard(
                    Uuid::fromString($groupCard['id']),
                    $groupCard['name'],
                );
            }
            $self->addGroup($group);
        }
        return $self;
    }

    public function addGroup(TableOfContentsGroup $group): void
    {
        $this->groups[] = $group;
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'groups' => $this->groups,
        ];
    }

    public function setCardSlides(int $inCardSlideId, array $slideIds): void
    {
        $found = false;
        foreach ($this->groups as $group) {
            $slide = $group->findSlide($inCardSlideId);
            if ($slide !== null) {
                $group->setCardSlides($slide['cardId'], $slideIds);
                $found = true;
            }
            if ($found) {
                break;
            }
        }
    }
}
