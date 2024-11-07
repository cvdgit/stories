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
        return array_map(
            static function ($row) {
                $createdAt = new \DateTimeImmutable('@' . $row['created_at']);
                $isNew = $createdAt->diff(new \DateTime())->days <= 7;
                return new Changelog(
                    (int) $row['id'],
                    $row['title'],
                    $row['text'],
                    $createdAt,
                    $isNew,
                );
            },
            $this->changelogApi->fetchLastList(),
        );
    }
}
