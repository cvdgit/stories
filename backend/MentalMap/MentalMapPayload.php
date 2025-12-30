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
    /**
     * @var array
     */
    private $settings = [];

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

    public static function planMentalMap(
        UuidInterface $id,
        string $name,
        string $text,
        array $treeData,
        UuidInterface $promptId = null,
        bool $accumulateFragments = false
    ): self {
        $obj = new self($id, $name, $text);
        $obj->treeView = true;
        $obj->settings = [
            'planTreeView' => true,
            'accumulateFragments' => $accumulateFragments,
        ];
        if ($promptId !== null) {
            $obj->settings['promptId'] = $promptId->toString();
        }
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
            'settings' => $this->settings,
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

    public static function filterEmptyFragments(array $fragments, string $titleKey = 'title'): array
    {
        return array_values(
            array_filter($fragments, static function (array $fragment) use ($titleKey): bool {
                return trim($fragment[$titleKey]) !== '';
            }),
        );
    }

    public static function accumulateFragments(array $fragments): array
    {
        $accumulationFragments = [];
        foreach ($fragments as $i => $fragment) {
            $accumulationFragments[] = array_reduce(
                array_slice($fragments, 0, $i + 1),
                static function (array $carry, array $item): array {
                    if (count($carry) === 0) {
                        return $item;
                    }
                    $carry['id'] = $item['id'];
                    $carry['title'] .= "\r\n" . $item['title'];
                    $carry['description'] .= "\r\n" . $item['description'];
                    return $carry;
                },
                [],
            );
        }
        return $accumulationFragments;
    }
}
