<?php

declare(strict_types=1);

namespace backend\Testing\columns;

class ColumnsMapper
{
    private $lists;

    public function __construct(array $lists)
    {
        $this->lists = $lists;
    }

    public function createColumns(int $source): ColumnListInterface
    {
        return $this->lists[$source];
    }
}
