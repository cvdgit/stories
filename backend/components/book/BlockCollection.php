<?php

namespace backend\components\book;

use ReflectionClass;

class BlockCollection implements \Iterator
{

    protected $className;
    protected $blocks = [];

    public function __construct($className)
    {
        $this->className = $className;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function isEmpty()
    {
        return count($this->blocks) === 0;
    }

    public function createBlock($arguments)
    {
        $reflector = new ReflectionClass($this->className);
        $block = $reflector->newInstanceArgs($arguments);
        $this->blocks[] = $block;
        return $block;
    }

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

    public function valid()
    {
        $key = key($this->blocks);
        return ($key !== NULL && $key !== FALSE);
    }

    public function rewind()
    {
        reset($this->blocks);
    }
}