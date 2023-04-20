<?php

declare(strict_types=1);

namespace backend\modules\changelog;

use backend\Changelog\ChangelogApiInterface;
use backend\modules\changelog\query\LastChangelogListFetcher;

class ChangelogApi implements ChangelogApiInterface
{
    public function fetchLastList(): array
    {
        return (new LastChangelogListFetcher())->fetch();
    }
}
