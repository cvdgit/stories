<?php

namespace common\models\test;

use common\components\BaseStatus;

class TestStatus extends BaseStatus
{

    public const DEFAULT = 0;
    public const TEMPLATE = 1;

    public static function asArray(): array
    {
        return [
            self::DEFAULT => 'По умолчанию',
            self::TEMPLATE => 'Шаблон',
        ];
    }

    public static function templatesNavItem(): array
    {
        return [
            ['label' => 'Шаблоны', 'url' => ['test/templates']],
        ];
    }
}
