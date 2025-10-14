<?php

declare(strict_types=1);

namespace backend\MentalMap;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class MentalMapPayload implements JsonSerializable
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $text;
    /**
     * @var bool
     */
    private $treeView;
    /**
     * @var array
     */
    private $treeData;

    private function __construct(UuidInterface $id, string $name, string $text)
    {
        $this->id = $id;
        $this->name = $name;
        $this->text = $text;
    }

    public static function treeMentalMap(UuidInterface $id, string $name, string $text, array $treeData): self
    {
        $obj = new self($id, $name, $text);
        $obj->treeView = true;
        $obj->treeData = $treeData;
        return $obj;
    }

    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'text' => $this->text,
            'treeView' => $this->treeView,
            'map' => [
                'url' => '/img/mental_map_blank.jpg',
                'width' => 1080,
                'height' => 720,
                'images' => [],
            ],
            'mapTypeIsMentalMapQuestions' => false,
            'treeData' => $this->treeData,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
