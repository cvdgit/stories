<?php

declare(strict_types=1);

namespace backend\Changelog;

interface ChangelogApiInterface
{
    /**
     * @return array<array-key, array{title: string, text: string, created_at: int}>
     */
    public function fetchLastList(): array;
}
