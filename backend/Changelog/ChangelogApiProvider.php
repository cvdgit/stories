<?php

declare(strict_types=1);

namespace backend\Changelog;

class ChangelogApiProvider
{
    private $changelogApi;

    public function __construct(ChangelogApiInterface $changelogApi)
    {
        $this->changelogApi = $changelogApi;
    }

    public function getChangelogLastItems(): array
    {
        return array_map(static function ($row) {
            return new Changelog(
                (int) $row['id'],
                $row['title'],
                $row['text'],
                new \DateTimeImmutable('@' . $row['created_at']),
            );
        }, $this->changelogApi->fetchLastList());
    }
}
