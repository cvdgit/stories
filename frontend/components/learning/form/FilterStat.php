<?php

declare(strict_types=1);

namespace frontend\components\learning\form;

final class FilterStat
{
    public const STAT_EDU = 'edu';
    public const STAT_WIKIDS = 'wikids';

    public static function getStatItems(): array
    {
        return [
            self::STAT_EDU => 'Обучение',
            self::STAT_WIKIDS => 'Wikids',
        ];
    }
}
