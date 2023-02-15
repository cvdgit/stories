<?php

declare(strict_types=1);

namespace common\components\dispatchers;

interface EventDispatcherInterface
{
    public function dispatchAll(array $events): void;

    public function dispatch($event): void;
}
