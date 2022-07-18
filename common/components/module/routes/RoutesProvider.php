<?php

namespace common\components\module\routes;

interface RoutesProvider
{
    public static function routes(): array;

    public static function routesPriority(): int;
}
