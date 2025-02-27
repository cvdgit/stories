<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

use yii\base\InvalidConfigException;

class SlideContentCollection
{
    private $contents;

    /**
     * @throws InvalidConfigException
     */
    public function __construct(array $contents)
    {
        foreach ($contents as $contentItem) {
            if (!$contentItem instanceof SlideContentItemInterface) {
                throw new InvalidConfigException('Type error');
            }
        }
        $this->contents = $contents;
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return array<T>
     */
    public function find(string $className): array
    {
        return array_filter($this->contents, static function (SlideContentItemInterface $item) use ($className): bool {
            return get_class($item) === $className;
        });
    }

    public function getContents(): array
    {
        return $this->contents;
    }
}
