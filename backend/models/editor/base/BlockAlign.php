<?php

namespace backend\models\editor\base;

class BlockAlign
{

    public const LEFT = 1;
    public const RIGHT = 2;
    public const TOP = 3;
    public const BOTTOM = 4;
    public const HORIZONTAL_CENTER = 5;
    public const VERTICAL_CENTER = 6;
    public const SLIDE_CENTER = 7;

    public static function asArray(): array
    {
        return [
            self::LEFT => 'По левому краю',
            self::RIGHT => 'По правому краю',
            self::TOP => 'По верху',
            self::BOTTOM => 'По низу',
            self::HORIZONTAL_CENTER => 'По центру (горизонтально)',
            self::VERTICAL_CENTER => 'По центру (вертикально)',
            self::SLIDE_CENTER => 'По центру слайда',
        ];
    }

    public static function asText(string $key): string
    {
        return self::asArray()[$key] ?? '';
    }

    public static function getDropdownItem(string $label, string $onclick): array
    {
        return [
            'label' => $label,
            'url' => '#',
            'linkOptions' => ['onclick' => $onclick],
        ];
    }

    public static function asDropdownItems(): array
    {
        return [
            self::getDropdownItem(self::asText(self::LEFT), 'StoryEditor.setBlockAlignLeft(); return false'),
            self::getDropdownItem(self::asText(self::RIGHT), 'StoryEditor.setBlockAlignRight(); return false'),
            self::getDropdownItem(self::asText(self::TOP), 'StoryEditor.setBlockAlignTop(); return false'),
            self::getDropdownItem(self::asText(self::BOTTOM), 'StoryEditor.setBlockAlignBottom(); return false'),
            self::getDropdownItem(self::asText(self::HORIZONTAL_CENTER), 'StoryEditor.setBlockAlignHorizontalCenter(); return false'),
            self::getDropdownItem(self::asText(self::VERTICAL_CENTER), 'StoryEditor.setBlockAlignVerticalCenter(); return false'),
            self::getDropdownItem(self::asText(self::SLIDE_CENTER), 'StoryEditor.setBlockAlignSlideCenter(); return false'),
        ];
    }

}