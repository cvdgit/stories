<?php

declare(strict_types=1);

namespace frontend\components;

interface UrlMatcherInterface
{
    public function match(string $url): ?array;
}
