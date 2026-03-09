<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

use DomainException;
use modules\edu\components\ArrayHelper;

class StoryStatCollection implements \ArrayAccess
{
    private $data;

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            throw new DomainException('Offset required');
        }
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function getTotalItems(): int
    {
        return array_reduce($this->data, static function (int $total, $items): int {
            return $total + count($items);
        }, 0);
    }

    public function filterStatRows(string $className, array $rows): int
    {
        $dataRows = $this->data[$className] ?? [];
        if (count($dataRows) === 0) {
            return count($rows);
        }

        $count = 0;
        foreach ($rows as $row) {
            $existsRow = ArrayHelper::array_find(
                $dataRows,
                static function (array $existsRow) use ($row): bool {
                    return $existsRow['id'] === $row['id'];
                },
            );
            if ($existsRow === null) {
                $count++;
                continue;
            }
            if ($existsRow['date'] === $row['date']) {
                $count++;
            }
        }

        return $count;
    }
}
