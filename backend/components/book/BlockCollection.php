<?php

declare(strict_types=1);

namespace backend\components\book;

use backend\components\book\blocks\GuestBlockInterface;
use Iterator;

/**
 * @template T
 */
class BlockCollection implements Iterator
{
    private $className;
    /** @var array <int, T> */
    private $blocks = [];

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return T
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function isEmpty(): bool
    {
        return count($this->blocks) === 0;
    }

    public function append(GuestBlockInterface $block): void
    {
        $this->blocks[] = $block;
    }

/*    public function createBlock($arguments)
    {
        $reflector = new ReflectionClass($this->className);
        $block = $reflector->newInstanceArgs($arguments);
        $this->blocks[] = $block;
        return $block;
    }*/

    public function current()
    {
        return current($this->blocks);
    }

    public function next()
    {
        return next($this->blocks);
    }

    public function key()
    {
        return key($this->blocks);
    }

    public function valid(): bool
    {
        $key = key($this->blocks);
        return $key !== null;
    }

    public function rewind(): void
    {
        reset($this->blocks);
    }
}
