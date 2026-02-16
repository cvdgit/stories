<?php

declare(strict_types=1);

namespace backend\TableOfContents;

use JsonSerializable;
use modules\edu\components\ArrayHelper;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TableOfContentsGroup implements JsonSerializable
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    private $slides = [];
    private $cards = [];

    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function addSlide(int $slideId, string $slideTitle, int $slideNumber, UuidInterface $cardId): void
    {
        $this->slides[] = [
            'id' => $slideId,
            'title' => $slideTitle,
            'slideNumber' => $slideNumber,
            'cardId' => $cardId->toString(),
        ];
    }

    public function addCard(UuidInterface $cardId, string $cardName): void
    {
        $this->cards[] = [
            'id' => $cardId->toString(),
            'name' => $cardName,
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'slides' => $this->slides,
            'cards' => $this->cards,
        ];
    }

    public function findSlide(int $slideId): ?array
    {
        return ArrayHelper::array_find(
            $this->slides,
            static function (array $row) use ($slideId): bool {
                return (int) $row['id'] === $slideId;
            },
        );
    }

    public function setCardSlides(string $cardId, array $slideIds): void
    {
        $this->slides = array_values(
            array_filter(
                $this->slides,
                static function (array $slide) use ($cardId): bool {
                    return $slide['cardId'] !== $cardId;
                },
            ),
        );

        foreach ($slideIds as $slideId) {
            $this->addSlide(
                $slideId,
                '',
                1,
                Uuid::fromString($cardId),
            );
        }
    }
}
